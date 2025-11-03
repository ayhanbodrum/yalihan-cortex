<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LocationSelector extends Component
{
    /**
     * Seçili ülke değeri
     *
     * @var string|null
     */
    public $selectedUlke;

    /**
     * Seçili il değeri
     *
     * @var string|null
     */
    public $selectedIl;

    /**
     * Seçili ilçe değeri
     *
     * @var string|null
     */
    public $selectedIlce;

    /**
     * Seçili mahalle değeri
     *
     * @var string|null
     */
    public $selectedMahalle;

    /**
     * Enlem (latitude) değeri
     *
     * @var float|null
     */
    public $latitude;

    /**
     * Boylam (longitude) değeri
     *
     * @var float|null
     */
    public $longitude;

    /**
     * Zoom seviyesi
     *
     * @var int
     */
    public $zoom;

    /**
     * Zorunlu alan olup olmadığı
     *
     * @var bool
     */
    public $required;

    /**
     * Etiket gösterim dili
     *
     * @var string
     */
    public $labelLang;

    /**
     * Etiketi göster/gizle
     *
     * @var bool
     */
    public $showLabels;

    /**
     * Haritayı göster/gizle
     *
     * @var bool
     */
    public $showMap;

    /**
     * Harita yüksekliği
     *
     * @var string
     */
    public $mapHeight;

    /**
     * Bileşen benzersiz kimliği
     *
     * @var string
     */
    public $uniqueId;

    /**
     * Yeni bir komponent örneği oluşturur.
     *
     * @param  string|null  $selectedUlke
     * @param  string|null  $selectedIl
     * @param  string|null  $selectedIlce
     * @param  string|null  $selectedMahalle
     * @param  float|null  $latitude
     * @param  float|null  $longitude
     * @param  int  $zoom
     * @param  bool  $required
     * @param  string  $labelLang
     * @param  bool  $showLabels
     * @param  bool  $showMap
     * @param  string  $mapHeight
     * @param  string|null  $uniqueId
     * @return void
     */
    public function __construct(
        $selectedUlke = 'TR',
        $selectedIl = null,
        $selectedIlce = null,
        $selectedMahalle = null,
        $latitude = 38.4237,
        $longitude = 27.1428,
        $zoom = 6,
        $required = false,
        $labelLang = 'tr',
        $showLabels = true,
        $showMap = true,
        $mapHeight = '400px',
        $uniqueId = null
    ) {
        $this->selectedUlke = $selectedUlke;
        $this->selectedIl = $selectedIl;
        $this->selectedIlce = $selectedIlce;
        $this->selectedMahalle = $selectedMahalle;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->zoom = $zoom;
        $this->required = $required;
        $this->labelLang = $labelLang;
        $this->showLabels = $showLabels;
        $this->showMap = $showMap;
        $this->mapHeight = $mapHeight;
        $this->uniqueId = $uniqueId ?: 'loc_'.uniqid();
    }

    /**
     * Komponentin gösterimini elde eder.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.location-selector');
    }
}
