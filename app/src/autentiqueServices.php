<?php

namespace App\src;

use App\src\appServices;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use vinicinbgs\Autentique\Documents;
use vinicinbgs\Autentique\Folders;

class AutentiqueServices
{
    /**
     * @var object
     */
    protected $autentiqueLogger;
    
    /**
     * @var object
     */
    protected $autentiqueEmailLogger;
    
    /**
     * @var Documents
     */
    private $documents;
    
    /**
     * @var Folders
     */
    private $folders;
    
    /**
     * @var bool
     */
    private $sandbox;
    
    /**
     * @var string|null
     */
    private $token;
    
    /**
     * @var string
     */
    private $downloadPath;

    /**
     * Class constructor
     *
     * @param bool $sandbox When true, sandbox mode is enabled.
     * @param string|null $token (Optional) — Used to override the session token.
     */
    public function __construct(bool $sandbox = false, ?string $token = null)
    {
        $appSrc = new appServices();
        
        // create a log channel
        $formatter = new LineFormatter(null, $_ENV['LOG_DATE_FORMAT']);
        
        $stream = $appSrc->_getStreamHandler();
        $stream->setFormatter($formatter);

        $this->autentiqueLogger  = new Logger('helpdezk');
        $this->autentiqueLogger->pushHandler($stream);
        
        // Clone the first one to only change the channel
        $this->autentiqueEmailLogger = $this->autentiqueLogger->withName('email');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se o token não for passado manualmente, busca da sessão
        $this->token = $token ?? ($_SESSION['autentique_token'] ?? '');

        if (empty($this->token)) {
            $this->autentiqueLogger->error("Autentique token was not found in session.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            throw new \Exception('Autentique token was not found in session.');
        }

        $this->documents = new Documents($this->token);
        $this->folders   = new Folders($this->token);

        $this->sandbox = $sandbox;
        $this->_configureMode();

        $storageDir = $appSrc->_setFolder($appSrc->_getHelpdezkPath().'/storage/');
        $downDir = $appSrc->_setFolder($storageDir.'downloads/');
        $tmpDir = $appSrc->_setFolder($downDir.'tmp/');
        $this->downloadPath = $appSrc->_setFolder($tmpDir.'signed-documents/');
    }

    /**
     * _configureMode
     * 
     * en_us 
     * pt_br Define o modo sandbox
     *
     * @return void
     */
    private function _configureMode(): void
    {
        $mode = $this->sandbox ? 'true' : 'false';
        $this->documents->setSandbox($mode);
        $this->folders->setSandbox($mode);
    }

    /** ====== DOCUMENTS ====== */
    
    /**
     * _createDocument
     * 
     * en_us 
     * pt_br 
     *
     * @param  mixed $filePath
     * @param  mixed $name
     * @param  mixed $signers
     * @param  mixed $folderId
     * @return array
     */
    public function _createDocument(string $filePath, string $name, array $signers, ?string $folderId = null): array
    {
        if (empty($filePath)) {
            $this->autentiqueLogger->error("No file path provided.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            throw new \Exception("No file path provided.");
        }

        if (!file_exists($filePath)) {
            $this->autentiqueLogger->error("No file path provided.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            throw new \Exception("Arquivo não encontrado: $filePath");
        }

        if (!is_readable($filePath)) {
            throw new \Exception("Arquivo encontrado, mas sem permissão de leitura: $filePath");
        }

        try {
            $attributes = [
                'document' => ['name' => $name],
                'signers'  => $signers,
                'file'     => $filePath, // o SDK espera o arquivo aqui
            ];

            if ($folderId) {
                $attributes['folder_id'] = $folderId;
            }

            $response = $this->documents->create($attributes);
            return $response ?? [];
        } catch (\Throwable $e) {
            $this->autentiqueLogger->error("No file path provided.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'Error' => $e->getMessage()]);
            throw new \Exception("Erro ao criar documento: " . $e->getMessage());
        }
    }
    
    /**
     * _listDocuments
     * 
     * en_us 
     * pt_br 
     *
     * @param  mixed $page
     * @param  mixed $limit
     * @return array
     */
    public function _listDocuments(int $page = 1, int $limit = 20): array
    {
        try {
            /* return $this->documents->listAll([
                'page'  => $page,
                'limit' => $limit
            ]); */
            return $this->documents->listAll(1);
        } catch (\Throwable $e) {
            $this->autentiqueLogger->error("Failed to retrieve the document data.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'Error' => $e->getMessage()]);
            throw new \Exception("Erro ao listar documentos: " . $e->getMessage());
        }
    }
    
    /**
     * _getDocument
     * 
     * en_us 
     * pt_br 
     *
     * @param  mixed $id
     * @return array
     */
    public function _getDocument(string $id): array
    {
        try {
            return $this->documents->listById($id);
        } catch (\Throwable $e) {
            $this->autentiqueLogger->error("No file path provided.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            throw new \Exception("Erro ao obter documento: " . $e->getMessage());
        }
    }
    
    /**
     * _deleteDocument
     * 
     * en_us 
     * pt_br 
     *
     * @param  mixed $id
     * @return bool
     */
    public function _deleteDocument(string $id): bool
    {
        try {
            $response = $this->documents->deleteById($id);
            return $response['data']['deleteDocument'] ?? false;
        } catch (\Throwable $e) {
            $this->autentiqueLogger->error("No file path provided.", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
            throw new \Exception("Erro ao excluir documento: " . $e->getMessage());
        }
    }
    
    /**
     * _downloadSignedDocument
     * 
     * en_us 
     * pt_br 
     *
     * @param  string $id
     * @return array
     */
    public function _downloadSignedDocument(string $id): array
    {
        try {
            // Obtém o documento
            $document = $this->documents->listById($id);

            if (empty($document['data']['document'])) {
                return [
                    'success' => false,
                    'status'  => 'not_found',
                    'message' => "Documento não encontrado para o ID: {$id}",
                    'data'    => null
                ];
            }

            $docData = $document['data']['document'];
            $files   = $docData['files'] ?? [];

            // Verifica se TODAS as assinaturas estão concluídas
            $allSigned = true;
            foreach ($docData['signatures'] as $sig) {
                // Assinaturas que NÃO exigem ação devem ser ignoradas
                if (empty($sig['action']) || empty($sig['action']['name'])) {
                    continue;
                }

                // Se exige ação e não está assinada → documento ainda não concluído
                if (empty($sig['signed'])) {
                    $allSigned = false;
                    break;
                }
            }

            if (!$allSigned) {
                return [
                    'success' => false,
                    'status'  => 'not_signed',
                    'message' => "Documento ainda não está totalmente assinado.",
                    'data'    => null
                ];
            }

            // A URL do assinado pode existir mas não estar disponível ainda
            $url = $files['signed'] ?? '';
            if (!$url) {
                return [
                    'success' => false,
                    'status'  => 'url_missing',
                    'message' => "URL do arquivo assinado não está disponível.",
                    'data'    => null
                ];
            }

            // Tenta baixar o PDF assinado
            $pdfContent = @file_get_contents($url);
            if ($pdfContent === false) {
                return [
                    'success' => false,
                    'status'  => 'download_failed',
                    'message' => "Falha ao baixar o arquivo assinado.",
                    'data'    => null
                ];
            }

            // Gera o nome do arquivo
            $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $docData['name']);
            $filename = sprintf('%s_signed_%s.pdf', $baseName, date('Ymd_His'));
            $filePath = $this->downloadPath . $filename;
            $fileUrl  = $_ENV['HDK_URL'] . '/storage/downloads/tmp/signed-documents/' . $filename;

            // Salva o arquivo
            if (@file_put_contents($filePath, $pdfContent) === false) {
                return [
                    'success' => false,
                    'status'  => 'save_failed',
                    'message' => "Falha ao salvar o arquivo assinado localmente.",
                    'data'    => null
                ];
            }

            // Retorno em caso de sucesso
            return [
                'success' => true,
                'status'  => 'ok',
                'message' => 'Arquivo assinado baixado com sucesso.',
                'data'    => [
                    'documentId'     => $id,
                    'documentName'   => $docData['name'] ?? 'sem_nome',
                    'signedFilePath' => $filePath,
                    'signedFileUrl'  => $fileUrl
                ]
            ];

        } catch (\Throwable $e) {

            // Erro inesperado (ex.: rede, parse, servidor)
            $this->autentiqueLogger->error("Erro inesperado ao baixar documento assinado", [
                'ex'        => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
                'id'        => $id,
                'Class'     => __CLASS__,
                'Method'    => __METHOD__,
                'Line'      => __LINE__
            ]);

            return [
                'success' => false,
                'status'  => 'exception',
                'message' => "Erro inesperado: " . $e->getMessage(),
                'data'    => null
            ];
        }
    }

    /**
     * _resendSignatures
     *
     * Reenvia os e-mails de assinatura para um ou mais signatários.
     *
     * @param  array  $publicIds
     * @return array
     */
    public function _resendSignatures(array $publicIds): array
    {
        try {

            $url = "https://api.autentique.com.br/v2/graphql";

            // Monta array JSON dos public_ids
            $publicIdsJson = json_encode($publicIds);

            // Monta a mutation EXATA que a API aceita
            $query = <<<GRAPHQL
                mutation {
                resendSignatures(public_ids: $publicIdsJson)
                }
            GRAPHQL;

            // Monta o payload JSON final
            $payload = json_encode([
                "query" => $query
            ]);

            // Inicializa CURL
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_POST           => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    "Authorization: Bearer $this->token",
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS     => $payload
            ]);

            // Executa e obtém resposta
            $response = curl_exec($ch);

            // Verifica erros de transporte
            if (curl_errno($ch)) {
                $this->autentiqueLogger->error("CURL error", [
                    'error'     =>  curl_error($ch),
                    'publicIds' => $publicIds
                ]);
                throw new \Exception("Erro CURL: " . curl_error($ch));
            }

            curl_close($ch);
            // Converte para array
            return json_decode($response, true);

        } catch (\Throwable $e) {
            $this->autentiqueLogger->error("Falha ao reenviar assinaturas", [
                'error'     => $e->getMessage(),
                'publicIds' => $publicIds
            ]);

            throw new \Exception("Erro ao reenviar assinaturas: " . $e->getMessage());
        }
    }

    /** ====== FOLDERS ====== */
    
    /**
     * _createFolder
     * 
     * en_us 
     * pt_br 
     *
     * @param  mixed $name
     * @return array
     */
    public function _createFolder(string $name): array
    {
        try {
            return $this->folders->create(['name' => $name]);
        } catch (\Throwable $e) {
            throw new \Exception("Erro ao criar pasta: " . $e->getMessage());
        }
    }
    
    /**
     * _listFolders
     * 
     * en_us 
     * pt_br 
     *
     * @return array
     */
    public function _listFolders(): array
    {
        try {
            return $this->folders->listAll();
        } catch (\Throwable $e) {
            throw new \Exception("Erro ao listar pastas: " . $e->getMessage());
        }
    }

    /** ====== OUTHERS ====== */
    
    /**
     * _setSandbox
     * 
     * en_us 
     * pt_br 
     *
     * @param  mixed $sandbox
     * @return void
     */
    public function _setSandbox(bool $sandbox): void
    {
        $this->sandbox = $sandbox;
        $this->_configureMode();
    }
    
    /**
     * _setToken
     * 
     * en_us 
     * pt_br 
     *
     * @param  mixed $token
     * @return void
     */
    public function _setToken(string $token): void
    {
        $this->token = $token;
        $_SESSION['autentique_token'] = $token;
    }
    
    /**
     * _getToken
     * 
     * en_us 
     * pt_br 
     *
     * @return string
     */
    public function _getToken(): string
    {
        return $this->token;
    }
}
