<?php

namespace App\Support;

class PixPayload
{
    public function __construct(
        public string $pixKey,
        public string $merchantName,
        public string $merchantCity,
        public string $txid,
        public float  $amount,
        public ?string $description = null,
    ) {}

    public function toString(): string
    {
        $gui   = $this->emv('00', 'BR.GOV.BCB.PIX');
        $key   = $this->emv('01', $this->pixKey);
        $desc  = $this->description ? $this->emv('02', mb_strimwidth($this->description, 0, 50)) : '';
        $mai   = $this->emv('26', $gui.$key.$desc);

        $pfi   = $this->emv('00', '01');          // Payload Format Indicator
        $mcc   = $this->emv('52', '0000');        // Merchant Category Code
        $cur   = $this->emv('53', '986');         // BRL
        $amt   = $this->emv('54', number_format($this->amount, 2, '.', ''));
        $ctry  = $this->emv('58', 'BR');
        $name  = $this->emv('59', mb_strimwidth($this->merchantName, 0, 25));
        $city  = $this->emv('60', mb_strimwidth($this->merchantCity, 0, 15));
        $txid  = $this->emv('05', $this->txid);   // dentro do 62
        $addtl = $this->emv('62', $txid);

        // 63 é CRC, calculado no final
        $payloadSemCRC = $pfi.$mai.$mcc.$cur.$amt.$ctry.$name.$city.$addtl.'6304';
        $crc = strtoupper($this->crc16($payloadSemCRC));

        return $payloadSemCRC.$crc;
    }

    private function emv(string $id, string $value): string
    {
        $len = mb_strlen($value, 'UTF-8');
        return $id . str_pad((string)$len, 2, '0', STR_PAD_LEFT) . $value;
    }

    private function crc16(string $str): string
    {
        $polynom = 0x1021; $crc = 0xFFFF;
        $bytes = array_values(unpack('C*', $str));
        foreach ($bytes as $b) {
            $crc ^= ($b << 8);
            for ($i=0; $i<8; $i++) {
                $crc = ($crc & 0x8000) ? ($crc << 1) ^ $polynom : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }
        return str_pad(dechex($crc), 4, '0', STR_PAD_LEFT);
    }
}
