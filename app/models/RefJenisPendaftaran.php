<?php

class RefJenisPendaftaran extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $Jenis_pendaftaran_id;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    public $Nama;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $Daftar_sekolah;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $Daftar_rombel;

    /**
     *
     * @var string
     * @Column(type="string", length=6, nullable=false)
     */
    public $Create_date;

    /**
     *
     * @var string
     * @Column(type="string", length=6, nullable=false)
     */
    public $Last_update;

    /**
     *
     * @var string
     * @Column(type="string", length=6, nullable=false)
     */
    public $Expired_date;

    /**
     *
     * @var string
     * @Column(type="string", length=6, nullable=false)
     */
    public $Last_sync;

    /**
     * Initialize method for model.
     */
    // public function initialize()
    // {
    //     $this->setSchema("siakad");
    //     $this->setSource("ref_jenis_pendaftaran");
    // }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'ref_jenis_pendaftaran';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return RefJenisPendaftaran[]|RefJenisPendaftaran|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RefJenisPendaftaran|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
