<?php

namespace App\modules\helpdezk\models\mysql;

final class evaluationModel
{    
    /**
     * @var int
     */
    private $idEvaluation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $iconName;
    
    /**
     * @var int
     */
    private $idQuestion;

    /**
     * @var string
     */
    private $status;
    
    /**
     * @var array
     */
    private $gridList; 
    
    /**
    * @var int
    */
    private $totalRows;

    /**
     * @var array
     */
    private $questionList;

    /**
     * @var array
     */
    private $answerList;
    
    /**
     * @var string
     */
    private $ticketCode;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $idCreator;

    /**
     * @var array
     */
    private $noteList;

    /**
     * @var int
     */
    private $idUserLog;

    /**
     * @var mixed
     */
    private $logDate;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var string
     */
    private $isApproved;

    /**
     * @var int
     */
    private $idStatus;

    /**
     * Get the value of idEvaluation
     *
     * @return  int
     */ 
    public function getIdEvaluation()
    {
        return $this->idEvaluation;
    }

    /**
     * Set the value of idEvaluation
     *
     * @param  int  $idEvaluation
     *
     * @return  self
     */ 
    public function setIdEvaluation(int $idEvaluation)
    {
        $this->idEvaluation = $idEvaluation;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of iconName
     *
     * @return  string
     */ 
    public function getIconName()
    {
        return $this->iconName;
    }

    /**
     * Set the value of iconName
     *
     * @param  string  $iconName
     *
     * @return  self
     */ 
    public function setIconName(string $iconName)
    {
        $this->iconName = $iconName;

        return $this;
    }

    /**
     * Get the value of idQuestion
     *
     * @return  int
     */ 
    public function getIdQuestion()
    {
        return $this->idQuestion;
    }

    /**
     * Set the value of idQuestion
     *
     * @param  int  $idQuestion
     *
     * @return  self
     */ 
    public function setIdQuestion(int $idQuestion)
    {
        $this->idQuestion = $idQuestion;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  string  $status
     *
     * @return  self
     */ 
    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of gridList
     *
     * @return  array
     */ 
    public function getGridList()
    {
        return $this->gridList;
    }

    /**
     * Set the value of gridList
     *
     * @param  array  $gridList
     *
     * @return  self
     */ 
    public function setGridList(array $gridList)
    {
        $this->gridList = $gridList;

        return $this;
    }

    /**
     * Get the value of totalRows
     *
     * @return  int
     */ 
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * Set the value of totalRows
     *
     * @param  int  $totalRows
     *
     * @return  self
     */ 
    public function setTotalRows(int $totalRows)
    {
        $this->totalRows = $totalRows;

        return $this;
    }

    /**
     * Get the value of questionList
     *
     * @return  array
     */ 
    public function getQuestionList()
    {
        return $this->questionList;
    }

    /**
     * Set the value of questionList
     *
     * @param  array  $questionList
     *
     * @return  self
     */ 
    public function setQuestionList(array $questionList)
    {
        $this->questionList = $questionList;

        return $this;
    }

    /**
     * Get the value of answerList
     *
     * @return  array
     */ 
    public function getAnswerList()
    {
        return $this->answerList;
    }

    /**
     * Set the value of answerList
     *
     * @param  array  $answerList
     *
     * @return  self
     */ 
    public function setAnswerList(array $answerList)
    {
        $this->answerList = $answerList;

        return $this;
    }

    /**
     * Get the value of ticketCode
     *
     * @return  string
     */ 
    public function getTicketCode()
    {
        return $this->ticketCode;
    }

    /**
     * Set the value of ticketCode
     *
     * @param  string  $ticketCode
     *
     * @return  self
     */ 
    public function setTicketCode(string $ticketCode)
    {
        $this->ticketCode = $ticketCode;

        return $this;
    }

    /**
     * Get the value of token
     *
     * @return  string
     */ 
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @param  string  $token
     *
     * @return  self
     */ 
    public function setToken(string $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of idCreator
     *
     * @return  int
     */ 
    public function getIdCreator()
    {
        return $this->idCreator;
    }

    /**
     * Set the value of idCreator
     *
     * @param  int  $idCreator
     *
     * @return  self
     */ 
    public function setIdCreator(int $idCreator)
    {
        $this->idCreator = $idCreator;

        return $this;
    }

    /**
     * Get the value of noteList
     *
     * @return  array
     */ 
    public function getNoteList()
    {
        return $this->noteList;
    }

    /**
     * Set the value of noteList
     *
     * @param  array  $noteList
     *
     * @return  self
     */ 
    public function setNoteList(array $noteList)
    {
        $this->noteList = $noteList;

        return $this;
    }

    /**
     * Get the value of idUserLog
     *
     * @return  int
     */ 
    public function getIdUserLog()
    {
        return $this->idUserLog;
    }

    /**
     * Set the value of idUserLog
     *
     * @param  int  $idUserLog
     *
     * @return  self
     */ 
    public function setIdUserLog(int $idUserLog)
    {
        $this->idUserLog = $idUserLog;

        return $this;
    }

    /**
     * Get the value of logDate
     *
     * @return  mixed
     */ 
    public function getLogDate()
    {
        return $this->logDate;
    }

    /**
     * Set the value of logDate
     *
     * @param  mixed  $logDate
     *
     * @return  self
     */ 
    public function setLogDate($logDate)
    {
        $this->logDate = $logDate;

        return $this;
    }

    /**
     * Get the value of comments
     *
     * @return  string
     */ 
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set the value of comments
     *
     * @param  string  $comments
     *
     * @return  self
     */ 
    public function setComments(string $comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get the value of isApproved
     *
     * @return  string
     */ 
    public function getIsApproved()
    {
        return $this->isApproved;
    }

    /**
     * Set the value of isApproved
     *
     * @param  string  $isApproved
     *
     * @return  self
     */ 
    public function setIsApproved(string $isApproved)
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    /**
     * Get the value of idStatus
     *
     * @return  int
     */ 
    public function getIdStatus()
    {
        return $this->idStatus;
    }

    /**
     * Set the value of idStatus
     *
     * @param  int  $idStatus
     *
     * @return  self
     */ 
    public function setIdStatus(int $idStatus)
    {
        $this->idStatus = $idStatus;

        return $this;
    }
}