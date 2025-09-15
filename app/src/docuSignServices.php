<?php
namespace App\src;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Tabs;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Client\ApiException;

class docuSignServices
{
    protected $clientId;
    protected $impersonatedUserId;
    protected $privateKeyPath;
    protected $oauthHost;
    protected $apiClient;
    protected $accountId;
    protected $docuSignLogger;
    protected $docuSignEmailLogger;

    public function __construct()
    {
        $appSrc = new appServices();
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);

        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->docuSignLogger  = new Logger('helpdezk');
        $this->docuSignLogger->pushHandler($stream);
        $this->docuSignEmailLogger = $this->docuSignLogger->withName('email');

        // Carrega variáveis de ambiente
        $this->clientId           = $_ENV['DS_CLIENT_ID'] ?? getenv('DS_CLIENT_ID');
        $this->impersonatedUserId = $_ENV['DS_IMPERSONATED_USER_ID'] ?? getenv('DS_IMPERSONATED_USER_ID');
        $this->privateKeyPath     = $_ENV['DS_PRIVATE_KEY_PATH'] ?? getenv('DS_PRIVATE_KEY_PATH');
        $this->oauthHost          = $_ENV['DS_OAUTH_HOST'] ?? getenv('DS_OAUTH_HOST') ?: 'account-d.docusign.com';

        $this->_authenticate();
    }

    private function _authenticate(): void
{
    // Lê a chave privada
    $privateKey = file_get_contents($this->privateKeyPath);
    if ($privateKey === false) {
        $this->docuSignLogger->error("Não foi possível ler a chave privada", [
            'privateKeyPath' => $this->privateKeyPath
        ]);
        throw new \Exception("Não foi possível ler a chave privada: {$this->privateKeyPath}");
    }

    // Configuração do client com o host correto
    $config = new Configuration();
    $config->setHost('https://demo.docusign.net/restapi'); // base URI default, será atualizado dinamicamente depois do login
    $this->apiClient = new ApiClient($config);

    // Define o ambiente OAuth
    $this->apiClient->getOAuth()->setOAuthBasePath($this->oauthHost);

    $scopes = ["signature", "impersonation"];
    $expiresIn = 3600;

    $this->docuSignLogger->debug("Tentando autenticar", [
        'clientId' => $this->clientId,
        'impersonatedUserId' => $this->impersonatedUserId,
        'oauthHost' => $this->oauthHost,
        'privateKeyPath' => $this->privateKeyPath
    ]);

    try {
        // Solicita o token JWT
        $oauthTokenArray = $this->apiClient->requestJWTUserToken(
            $this->clientId,
            $this->impersonatedUserId,
            $privateKey,
            $scopes,
            $expiresIn
        );
    } catch (\Exception $ex) {
        $this->docuSignLogger->error("Falha ao gerar token JWT", [
            'message' => $ex->getMessage(),
            'trace' => $ex->getTraceAsString()
        ]);
        throw new \Exception("Falha ao gerar token JWT: " . $ex->getMessage());
    }

    // Extrai o access token
    if (is_array($oauthTokenArray) && isset($oauthTokenArray[0]) && $oauthTokenArray[0] instanceof \DocuSign\eSign\Client\Auth\OAuthToken) {
        $accessToken = $oauthTokenArray[0]->getAccessToken();
    } else {
        $this->docuSignLogger->error("Não foi possível extrair access_token do JWT", [
            'oauthToken' => $oauthTokenArray
        ]);
        throw new \Exception("Não foi possível extrair access_token do JWT");
    }

    // Configura o access token no client
    $this->apiClient->getConfig()->setAccessToken($accessToken, $expiresIn);

    // Obtém informações do usuário
    try {
        $userInfo = $this->apiClient->getUserInfo($accessToken);
        $account = $userInfo[0]["accounts"][0] ?? null;

        if (empty($account['account_id']) || empty($account['base_uri'])) {
            throw new \Exception("account_id ou base_uri não encontrados na conta do usuário");
        }

        $this->accountId = $account['account_id'];

        // Atualiza dinamicamente o host do ApiClient para a conta real
        $this->apiClient->getConfig()->setHost(rtrim($account['base_uri'], '/') . '/restapi');

        // Logging seguro das contas
        $safeAccounts = array_map(function($acc) {
            return [
                'account_id' => $acc['account_id'] ?? null,
                'account_name' => $acc['account_name'] ?? null,
                'base_uri' => $acc['base_uri'] ?? null
            ];
        }, $userInfo[0]["accounts"]);

        $this->docuSignLogger->info("Autenticação DocuSign concluída com sucesso", [
            'accountId' => $this->accountId,
            'baseUri' => $account['base_uri'],
            'total_accounts' => count($safeAccounts)
        ]);

    } catch (\Exception $ex) {
        $this->docuSignLogger->error("Falha ao obter informações do usuário", [
            'message' => $ex->getMessage(),
            'trace' => $ex->getTraceAsString()
        ]);
        throw new \Exception("Falha ao obter informações do usuário: " . $ex->getMessage());
    }
}


    public function _sendEnvelope(string $pdfPath, array $signers): string
    {
        if (!file_exists($pdfPath)) {
            $message = "Arquivo não encontrado: $pdfPath";
            $this->docuSignLogger->error($message);
            throw new \Exception($message);
        }

        $docBytes = file_get_contents($pdfPath);
        $document = new Document([
            'document_base64' => base64_encode($docBytes),
            'name'            => basename($pdfPath),
            'file_extension'  => 'pdf',
            'document_id'     => '1'
        ]);

        $signerObjs = [];
        $recipientId = 1;

        foreach ($signers as $order => $signerData) {
            if (!isset($signerData['email'], $signerData['name'], $signerData['anchor'])) {
                $message = "Cada signatário precisa ter email, name e anchor.";
                $this->docuSignLogger->error($message, ['signerData' => $signerData]);
                throw new \Exception($message);
            }

            $signer = new Signer([
                'email'         => $signerData['email'],
                'name'          => $signerData['name'],
                'recipient_id'  => (string)$recipientId,
                'routing_order' => (string)($order + 1)
            ]);

            $signHere = new SignHere([
                'anchor_string'   => $signerData['anchor'],
                'anchor_units'    => 'pixels',
                'anchor_x_offset' => '0',
                'anchor_y_offset' => '0',
                'recipient_id'    => (string)$recipientId,
                'document_id'     => '1'
            ]);

            $tabs = new Tabs(['sign_here_tabs' => [$signHere]]);
            $signer->setTabs($tabs);

            $signerObjs[] = $signer;
            $recipientId++;
        }

        $recipients = new Recipients(['signers' => $signerObjs]);
        $envelopeDefinition = new EnvelopeDefinition([
            'email_subject' => 'Por favor assinem o documento',
            'documents'     => [$document],
            'recipients'    => $recipients,
            'status'        => 'sent'
        ]);

        $envelopeApi = new EnvelopesApi($this->apiClient);

        try {
            $result = $envelopeApi->createEnvelope($this->accountId, $envelopeDefinition);
            $this->docuSignLogger->debug("Envelope enviado com sucesso.", ['envelopeId' => $result->getEnvelopeId()]);
            return $result->getEnvelopeId();
        } catch (ApiException $ex) {
            $responseBody = $ex->getResponseBody();
            $message = "Erro ao criar envelope: " . $ex->getMessage();
            if ($responseBody) {
                $message .= "\nDetalhes da API: " . json_encode($responseBody, JSON_PRETTY_PRINT);
            }
            $this->docuSignLogger->error($message, ['exception' => $ex]);
            throw new \Exception($message);
        }
    }
}
