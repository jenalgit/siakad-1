<?php

class RefStatusKepegawaian extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=4, nullable=false)
     */
    public $Status_kepegawaian_id;

    /**
     *
     * @var string
     * @Column(type="string", length=30, nullable=false)
     */
    public $Nama;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $Create_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $Last_update;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $Expired_date;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $Last_sync;

    /**
     * Initialize method for model.
     */
    // public function initialize()
    // {
    //     $this->setSchema("siakad");
    //     $this->setSource("ref_status_kepegawaian");
    // }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'ref_status_kepegawaian';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return RefStatusKepegawaian[]|RefStatusKepegawaian|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RefStatusKepegawaian|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
