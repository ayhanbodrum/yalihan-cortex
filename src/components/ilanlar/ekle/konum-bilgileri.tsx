"use client";

import * as React from "react";
import { useState, useRef } from "react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card } from "@/components/ui/card";
import { cn } from "@/lib/utils";
import { Harita } from "./harita";
import { AutocompleteSelect } from "@/components/common/autocomplete-select";

interface KonumBilgileriProps {
    formData: any;
    setFormData: (data: any) => void;
}

interface AddressResult {
    id: string;
    label: string;
    il: string;
    ilce: string;
    mahalle: string;
    lat: number;
    lng: number;
    postaKodu?: string;
}

export function KonumBilgileri({ formData, setFormData }: KonumBilgileriProps) {
    const [search, setSearch] = useState("");
    const [results, setResults] = useState<AddressResult[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const debounceRef = useRef<NodeJS.Timeout | null>(null);

    // Adres arama fonksiyonu
    const handleSearch = (value: string) => {
        setSearch(value);
        setError("");
        if (debounceRef.current) clearTimeout(debounceRef.current);
        if (value.length < 2) {
            setResults([]);
            return;
        }
        setLoading(true);
        debounceRef.current = setTimeout(async () => {
            try {
                const res = await fetch(`http://127.0.0.1:8002/api/address/search?q=${encodeURIComponent(value)}`);
                if (!res.ok) throw new Error("Adres arama başarısız oldu");
                const data = await res.json();
                setResults(data.results || []);
            } catch (err) {
                setError("Adres arama sırasında hata oluştu.");
                setResults([]);
            } finally {
                setLoading(false);
            }
        }, 300);
    };

    // Adres seçildiğinde formu güncelle
    const handleSelect = (item: AddressResult) => {
        setFormData((prev: any) => ({
            ...prev,
            il: item.il,
            ilce: item.ilce,
            mahalle: item.mahalle,
            lat: item.lat,
            lng: item.lng,
            adres: item.label,
            postaKodu: item.postaKodu || "",
        }));
        setSearch(item.label);
        setResults([]);
    };

    // Manuel alan değişikliği
    const handleFieldChange = (field: string, value: any) => {
        setFormData((prev: any) => ({ ...prev, [field]: value }));
    };

    return (
        <div className="space-y-6">
            <Card className="p-6">
                <Label htmlFor="adresArama">Adres Ara ve Seç</Label>
                <div className="relative mt-2 mb-6">
                    <Input
                        id="adresArama"
                        value={search}
                        onChange={(e) => handleSearch(e.target.value)}
                        placeholder="İl, ilçe, mahalle veya tam adres yazarak arayabilirsiniz"
                        autoComplete="off"
                    />
                    {loading && (
                        <span className="absolute right-3 top-3 text-xs text-gray-400">Yükleniyor...</span>
                    )}
                    {error && (
                        <span className="block text-xs text-red-500 mt-1">{error}</span>
                    )}
                    {results.length > 0 && (
                        <div className="absolute z-10 w-full bg-white border rounded shadow mt-1 max-h-60 overflow-auto">
                            {results.map((item) => (
                                <div
                                    key={item.id}
                                    className={cn(
                                        "px-4 py-2 cursor-pointer hover:bg-gray-100 text-sm",
                                        search === item.label && "bg-gray-100"
                                    )}
                                    onClick={() => handleSelect(item)}
                                    tabIndex={0}
                                    aria-label={`Adres seç: ${item.label}`}
                                    onKeyDown={(e) => e.key === "Enter" && handleSelect(item)}
                                >
                                    {item.label}
                                    <span className="ml-2 text-xs text-gray-400">({item.il} / {item.ilce} / {item.mahalle})</span>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <Label htmlFor="ulke">Ülke</Label>
                        <Input id="ulke" value="Türkiye" disabled className="bg-gray-100" />
                    </div>
                    <div>
                        <Label htmlFor="il">İl</Label>
                        <Input id="il" value={formData.il || ""} onChange={e => handleFieldChange("il", e.target.value)} placeholder="İl" />
                    </div>
                    <div>
                        <Label htmlFor="ilce">İlçe</Label>
                        <Input id="ilce" value={formData.ilce || ""} onChange={e => handleFieldChange("ilce", e.target.value)} placeholder="İlçe" />
                    </div>
                    <div>
                        <Label htmlFor="mahalle">Mahalle</Label>
                        <Input id="mahalle" value={formData.mahalle || ""} onChange={e => handleFieldChange("mahalle", e.target.value)} placeholder="Mahalle" />
                    </div>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <Label htmlFor="site">Site/Apartman Adı</Label>
                        <AutocompleteSelect
                            searchApi="http://127.0.0.1:8002/api/sites/search"
                            placeholder="Site veya apartman adı"
                            labelKey="ad"
                            onSelect={item => handleFieldChange("site", item.ad)}
                            onCreateNew={async (value) => {
                                const res = await fetch("http://127.0.0.1:8002/api/sites/create", {
                                    method: "POST",
                                    headers: { "Content-Type": "application/json" },
                                    body: JSON.stringify({ ad: value })
                                });
                                if (res.ok) {
                                    const data = await res.json();
                                    return data.site;
                                }
                                return null;
                            }}
                        />
                    </div>
                    <div>
                        <Label htmlFor="sokak">Sokak/Cadde</Label>
                        <Input id="sokak" value={formData.sokak || ""} onChange={e => handleFieldChange("sokak", e.target.value)} placeholder="Sokak veya cadde adı" />
                    </div>
                    <div>
                        <Label htmlFor="blok">Blok/Daire No</Label>
                        <Input id="blok" value={formData.blok || ""} onChange={e => handleFieldChange("blok", e.target.value)} placeholder="A Blok Daire 5" />
                    </div>
                    <div>
                        <Label htmlFor="postaKodu">Posta Kodu</Label>
                        <Input id="postaKodu" value={formData.postaKodu || ""} onChange={e => handleFieldChange("postaKodu", e.target.value)} placeholder="34000" />
                    </div>
                </div>
                <div className="mb-4">
                    <Label htmlFor="adresTarifi">Detaylı Adres Tarifi</Label>
                    <Input id="adresTarifi" value={formData.adresTarifi || ""} onChange={e => handleFieldChange("adresTarifi", e.target.value)} placeholder="Ek adres bilgileri, tarif, yakın yerler..." />
                </div>
            </Card>
            <Card className="p-6">
                <Label>Harita Konumu</Label>
                <div className="flex gap-2 mb-2">
                    <button
                        type="button"
                        className="px-3 py-1 rounded bg-gray-200 text-black text-sm"
                        onClick={() => {
                            // Haritayı Yenile fonksiyonu (Leaflet harita boyutunu güncellemek için)
                            const mapDiv = document.querySelector(".leaflet-container");
                            if (mapDiv && mapDiv._leaflet_id) {
                                const map = window.L && window.L.map && window.L.map(mapDiv);
                                if (map && map.invalidateSize) map.invalidateSize();
                            }
                        }}
                    >
                        Haritayı Yenile
                    </button>
                    <button
                        type="button"
                        className="px-3 py-1 rounded bg-[#7FFFD4] text-black text-sm"
                        onClick={() => {
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition((pos) => {
                                    setFormData((prev: any) => ({ ...prev, lat: pos.coords.latitude, lng: pos.coords.longitude }));
                                });
                            }
                        }}
                    >
                        Mevcut Konumum
                    </button>
                </div>
                <div className="w-full h-64 rounded mt-2">
                    <Harita
                        lat={formData.lat ? Number(formData.lat) : null}
                        lng={formData.lng ? Number(formData.lng) : null}
                        onChange={(lat, lng) => {
                            setFormData((prev: any) => ({ ...prev, lat, lng }));
                        }}
                    />
                </div>
            </Card>
        </div>
    );
}
