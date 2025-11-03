"use client";

import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { IlanTemelBilgiler } from "@/components/ilanlar/ekle/ilan-temel-bilgiler";
import { KonumBilgileri } from "@/components/ilanlar/ekle/konum-bilgileri";
import { Ozellikler } from "@/components/ilanlar/ekle/ozellikler";
import { MedyaYonetimi } from "@/components/ilanlar/ekle/medya-yonetimi";
import { IletisimBilgileri } from "@/components/ilanlar/ekle/iletisim-bilgileri";

export default function IlanEklePage() {
    const [activeTab, setActiveTab] = useState("temel");
    const [formData, setFormData] = useState({});

    const handleTabChange = (value: string) => {
        setActiveTab(value);
    };

    const handleFormSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        // Form gönderme işlemleri
    };

    return (
        <div className="container mx-auto py-6">
            <Card className="p-6">
                <h1 className="text-2xl font-bold mb-6">Yeni İlan Ekle</h1>

                <form onSubmit={handleFormSubmit}>
                    <Tabs value={activeTab} onValueChange={handleTabChange}>
                        <TabsList className="grid w-full grid-cols-5">
                            <TabsTrigger value="temel">Temel Bilgiler</TabsTrigger>
                            <TabsTrigger value="konum">Konum</TabsTrigger>
                            <TabsTrigger value="ozellikler">Özellikler</TabsTrigger>
                            <TabsTrigger value="medya">Medya</TabsTrigger>
                            <TabsTrigger value="iletisim">İletişim</TabsTrigger>
                        </TabsList>

                        <TabsContent value="temel">
                            <IlanTemelBilgiler formData={formData} setFormData={setFormData} />
                        </TabsContent>

                        <TabsContent value="konum">
                            <KonumBilgileri formData={formData} setFormData={setFormData} />
                        </TabsContent>

                        <TabsContent value="ozellikler">
                            <Ozellikler formData={formData} setFormData={setFormData} />
                        </TabsContent>

                        <TabsContent value="medya">
                            <MedyaYonetimi formData={formData} setFormData={setFormData} />
                        </TabsContent>

                        <TabsContent value="iletisim">
                            <IletisimBilgileri formData={formData} setFormData={setFormData} />
                        </TabsContent>
                    </Tabs>

                    <div className="flex justify-end gap-4 mt-6">
                        <Button variant="outline" type="button">
                            Taslak Olarak Kaydet
                        </Button>
                        <Button type="submit">
                            İlanı Yayınla
                        </Button>
                    </div>
                </form>
            </Card>
        </div>
    );
} 