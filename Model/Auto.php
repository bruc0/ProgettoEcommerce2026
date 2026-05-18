<?php
enum Carburante: string {
    case Benzina = 'Benzina';
    case Diesel = 'Diesel';
    case Elettrica = 'Elettrica';
    case Ibrida = 'Ibrida';
    case GPL = 'GPL';
}

enum TipoVenditore: string {
    case Concessionario = 'Concessionario';
    case Privato = 'Privato';
}

class Auto {
    public function __construct(
        public string $marca,
        public string $modello,
        public int $prezzo,
        public int $chilometraggio,
        public DateTime $immatricolazione, // Mese/Anno come su AutoScout
        public Carburante $carburante,
        public int $potenzaCv,             // Filtro CV/kW molto usato
        public string $colore,
        public bool $garanzia,
        public array $optional = [],       // Es: ['Sensori', 'CarPlay', 'LED']
        public int $porte = 5,
        public TipoVenditore $tipoVenditore
    ) {}

    public function getMarca(): string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): self
    {
        $this->marca = $marca;
        return $this;
    }

    public function getModello(): string
    {
        return $this->modello;
    }

    public function setModello(string $modello): self
    {
        $this->modello = $modello;
        return $this;
    }

    public function getPrezzo(): int
    {
        return $this->prezzo;
    }

    public function setPrezzo(int $prezzo): self
    {
        $this->prezzo = $prezzo;
        return $this;
    }

    public function getChilometraggio(): int
    {
        return $this->chilometraggio;
    }

    public function setChilometraggio(int $chilometraggio): self
    {
        $this->chilometraggio = $chilometraggio;
        return $this;
    }

    public function getImmatricolazione(): DateTime
    {
        return $this->immatricolazione;
    }

    public function setImmatricolazione(DateTime $immatricolazione): self
    {
        $this->immatricolazione = $immatricolazione;
        return $this;
    }

    public function getCarburante(): Carburante
    {
        return $this->carburante;
    }

    public function setCarburante(Carburante $carburante): self
    {
        $this->carburante = $carburante;
        return $this;
    }

    public function getPotenzaCv(): int
    {
        return $this->potenzaCv;
    }

    public function setPotenzaCv(int $potenzaCv): self
    {
        $this->potenzaCv = $potenzaCv;
        return $this;
    }

    public function getColore(): string
    {
        return $this->colore;
    }

    public function setColore(string $colore): self
    {
        $this->colore = $colore;
        return $this;
    }

    public function getGaranzia(): bool
    {
        return $this->garanzia;
    }

    public function setGaranzia(bool $garanzia): self
    {
        $this->garanzia = $garanzia;
        return $this;
    }

    public function getOptional(): array
    {
        return $this->optional;
    }

    public function setOptional(array $optional): self
    {
        $this->optional = $optional;
        return $this;
    }

    public function getPorte(): int
    {
        return $this->porte;
    }

    public function setPorte(int $porte): self
    {
        $this->porte = $porte;
        return $this;
    }

    public function getTipoVenditore(): TipoVenditore
    {
        return $this->tipoVenditore;
    }

    public function setTipoVenditore(TipoVenditore $tipoVenditore): self
    {
        $this->tipoVenditore = $tipoVenditore;
        return $this;
    }
}
?>