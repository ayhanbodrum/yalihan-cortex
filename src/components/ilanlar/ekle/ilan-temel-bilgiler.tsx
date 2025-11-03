"use client";

import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card } from "@/components/ui/card";

interface IlanTemelBilgilerProps {
    formData: any;
    setFormData: (data: any) => void;
}

export function IlanTemelBilgiler({ formData, setFormData }: IlanTemelBilgilerProps) {
    const handleChange = (field: string, value: any) => {
        setFormData((prev: any) => ({
            ...prev,
            [field]: value
        }));
    };

    return (
        <div className="space-y-6">
            <Card className="p-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                        <Label htmlFor="ilanTuru">İlan Türü</Label>
                        <Select
                            value={formData.ilanTuru}
                            onValueChange={(value) => handleChange("ilanTuru", value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="İlan türü seçin" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="konut">Konut</SelectItem>
                                <SelectItem value="arsa">Arsa</SelectItem>
                                <SelectItem value="isyeri">İşyeri</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="yayinlamaTipi">Yayınlama Tipi</Label>
                        <Select
                            value={formData.yayinlamaTipi}
                            onValueChange={(value) => handleChange("yayinlamaTipi", value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Yayınlama tipi seçin" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="satilik">Satılık</SelectItem>
                                <SelectItem value="kiralik">Kiralık</SelectItem>
                                <SelectItem value="sezonluk">Sezonluk Kiralık</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="fiyat">Fiyat</Label>
                        <Input
                            id="fiyat"
                            type="number"
                            value={formData.fiyat}
                            onChange={(e) => handleChange("fiyat", e.target.value)}
                            placeholder="Fiyat girin"
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="paraBirimi">Para Birimi</Label>
                        <Select
                            value={formData.paraBirimi}
                            onValueChange={(value) => handleChange("paraBirimi", value)}
                        >
                            <SelectTrigger>
                                <SelectValue placeholder="Para birimi seçin" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="TRY">TL</SelectItem>
                                <SelectItem value="USD">USD</SelectItem>
                                <SelectItem value="EUR">EUR</SelectItem>
                                <SelectItem value="GBP">GBP</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </Card>

            <Card className="p-6">
                <div className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="baslik">İlan Başlığı</Label>
                        <Input
                            id="baslik"
                            value={formData.baslik}
                            onChange={(e) => handleChange("baslik", e.target.value)}
                            placeholder="İlan başlığı girin"
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="aciklama">İlan Açıklaması</Label>
                        <Textarea
                            id="aciklama"
                            value={formData.aciklama}
                            onChange={(e) => handleChange("aciklama", e.target.value)}
                            placeholder="İlan açıklaması girin"
                            rows={6}
                        />
                    </div>
                </div>
            </Card>
        </div>
    );
} 