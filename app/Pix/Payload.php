
<?php

namespace App\Pix;

class Payload{
 /**
   * IDs do Payload do Pix
   * @var string
   */
  const ID_PAYLOAD_FORMAT_INDICATOR = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
  const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
  const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
  const ID_MERCHANT_CATEGORY_CODE = '52';
  const ID_TRANSACTION_CURRENCY = '53';
  const ID_TRANSACTION_AMOUNT = '54';
  const ID_COUNTRY_CODE = '58';
  const ID_MERCHANT_NAME = '59';
  const ID_MERCHANT_CITY = '60';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
  const ID_CRC16 = '63';

  /** 
  * Char pix
  * @var string
  */
  private $pixKey;

    /** 
  * Nome do titular da conta
  * @var string
  */
  private $description;

    /** 
  * Name do titular da conta
  * @var string
  */
  private $merchantName;

  /** 
  * Cidade do titular da conta
  * @var string
  */
  private $merchantCity;


  /** 
  * ID da transação do pix
  * @var string
  */
  private $txid;

  /** 
  * Valor da transação
  * @var string
  */
  private $amount;


  /** 
  * Método responsável por definir o valor de $pixKey
  * @param string $pixKey
  */
  public function setPixKey($pixKey){
      $this->pixKey = $pixKey;
      return $this;
  }


  /** 
  * Método responsável por definir o valor de $Description
  * @param string $Description
  */
  public function setDescription($description){
    $this->description = $description;
    return $this;
  }


    /** 
  * Método responsável por definir o valor de $MerchantName
  * @param string $MerchantName
  */
    public function setMerchantName($MerchantName){
        $this->merchantName = $MerchantName;
        return $this;
    }


    /** 
    * Método responsável por definir o valor de $setMerchantCity
    * @param string $setMerchantCity
    */
    public function setMerchantCity($MerchantCity){
        $this->merchantCity = $MerchantCity;
        return $this;
    }

    /** 
    * Método responsável por definir o valor de $Txid
    * @param string $Txid
    */
    public function setTxid($Txid){
        $this->Txid = $Txid;
        return $this;
    }


    /** 
    * Método responsável por definir o valor de $Amount
    * @param float $Amount
    */
    public function setAmount($Amount){
        $this->Amount = (string) number_format($Amount, 2, '.', '');
        return $this;
    }


    /** 
    * Responsável por retornar o valor completo de um objeto de payload
    * @param string $id
    * @param string $value
    * @return string $id.$size.$value
    */
    private function getValue($id, $value){
        $size = str_pad(strlen($value),2,'0',STR_PAD_LEFT);
        return $id.$size.$value;
    }



    /**
    * Metodo responsavel por retornar os valores completos da informação da conta
    * @return string
    */
    public function getMerchantAccountInformation(){
      //DOMINIO DO BANCO
      $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');

      //CHAVE PIX
      $key = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY,$this->pixKey);

      //$description = strlen($this->description) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';
      $description = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION,$this->description);

      //VALOR COMPLETO DA CONTA
      return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$description);
    }


     /**
    * Metodo responsavel por retornar os valores completos do campo adicional do pix (TXID)
    * @return string
    */
    private function getAdditionalDataFieldTemplate(){
      //TXID

      $txid = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);

      return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
      
    }


    /**
    * Metodo responsavel por gerar o codigo completo do payload Pix
    * @return string
    */
    public function getPayload(){
      //CRIA o PAYLOAD
      $payload = $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR, '01').$this->getMerchantAccountInformation().
      $this->getValue(self::ID_MERCHANT_CATEGORY_CODE, '0000').
      $this->getValue(self::ID_TRANSACTION_CURRENCY, '986').
      $this->getValue(self::ID_TRANSACTION_AMOUNT, $this->amount).
      $this->getValue(self::ID_COUNTRY_CODE, 'BR').
      $this->getValue(self::ID_MERCHANT_NAME, $this->merchantName).
      $this->getValue(self::ID_MERCHANT_CITY, $this->merchantCity).
      $this->getAdditionalDataFieldTemplate();

      //Retorna o payload + CR16
      return $payload.$this->getCRC16($payload);
    }


      /**
   * Método responsável por calcular o valor da hash de validação do código pix
   * @return string
   */
  private function getCRC16($payload) {
      //ADICIONA DADOS GERAIS NO PAYLOAD
      $payload .= self::ID_CRC16.'04';

      //DADOS DEFINIDOS PELO BACEN
      $polinomio = 0x1021;
      $resultado = 0xFFFF;

      //CHECKSUM
      if (($length = strlen($payload)) > 0) {
          for ($offset = 0; $offset < $length; $offset++) {
              $resultado ^= (ord($payload[$offset]) << 8);
              for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                  if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                  $resultado &= 0xFFFF;
              }
          }
      }

      //RETORNA CÓDIGO CRC16 DE 4 CARACTERES
      return self::ID_CRC16.'04'.strtoupper(dechex($resultado));
  }

}

?>