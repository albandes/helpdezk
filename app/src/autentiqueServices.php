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
            // Obtém o documento pelo ID
            $document = $this->documents->listById($id);

            if (empty($document['data']['document'])) {
                $this->autentiqueLogger->error("Documento não encontrado para o ID {$id}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
                throw new \Exception("Documento não encontrado para o ID: {$id}");
            }

            $docData = $document['data']['document'];
            $files = $docData['files'] ?? [];

            // Verifica se o arquivo assinado existe
            if (empty($files['signed'])) {
                $this->autentiqueLogger->error("'Signed' file not found for document ID {$id}", ['Class' => __CLASS__, 'Method' => __METHOD__,'Line' => __LINE__,'files' => $files]);
                throw new \Exception("'Signed' file not found for document ID {$id}");
            }

            $url = $files['signed'];
            if (!$url) {
                $this->autentiqueLogger->error("Download URL not found for document ID {$id}", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__]);
                throw new \Exception("Download URL not found for document ID {$id}");
            }

            // Gera o nome do arquivo
            $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $docData['name']);
            $filename = sprintf('%s_signed_%s.pdf', $baseName, date('Ymd_His'));
            $filePath = $this->downloadPath . $filename;
            $fileUrl = $_ENV['HDK_URL'] . '/storage/downloads/tmp/signed-documents/' . $filename;

            // Faz o download do PDF assinado
            $pdfContent = @file_get_contents($url);
            if ($pdfContent === false) {
                $this->autentiqueLogger->error("Falha ao baixar arquivo assinado", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'url' => $url, 'document_id' => $id]);
                throw new \Exception("Falha ao baixar o arquivo assinado para o documento ID: {$id}");
            }

            // Salva localmente
            if (@file_put_contents($filePath, $pdfContent) === false) {
                $this->autentiqueLogger->error("Falha ao salvar arquivo localmente", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'filePath' => $filePath]);
                throw new \Exception("Falha ao salvar o arquivo assinado localmente: {$filePath}");
            }

            // Retorna os dados do arquivo baixado
            return [
                'documentId' => $id,
                'documentName' => $docData['name'] ?? 'sem_nome',
                'signedFilePath' => $filePath,
                'signedFileUrl' => $fileUrl
            ];

        } catch (\Throwable $e) {
            $this->autentiqueLogger->error("Erro ao baixar documento assinado", ['Class' => __CLASS__, 'Method' => __METHOD__, 'Line' => __LINE__, 'id' => $id, 'mensagem' => $e->getMessage(),'trace' => $e->getTraceAsString()]);

            throw new \Exception("Erro ao baixar documento assinado: " . $e->getMessage());
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

            if (empty($publicIds)) {
                throw new \Exception("publicIds cannot be empty");
            }

            return $this->documents->resendSignatures($publicIds);

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
