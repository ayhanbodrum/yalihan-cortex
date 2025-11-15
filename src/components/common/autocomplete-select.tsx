"use client";

import * as React from "react";
import { useState, useRef } from "react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";

interface AutocompleteSelectProps {
    searchApi: string;
    placeholder?: string;
    labelKey: string;
    onSelect: (item: any) => void;
    onCreateNew?: (value: string) => Promise<any>;
}

export function AutocompleteSelect({ searchApi, placeholder, labelKey, onSelect, onCreateNew }: AutocompleteSelectProps) {
    const [search, setSearch] = useState("");
    const [results, setResults] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const [showModal, setShowModal] = useState(false);
    const [newValue, setNewValue] = useState("");
    const debounceRef = useRef<NodeJS.Timeout | null>(null);

    // Canlı arama fonksiyonu
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
                const res = await fetch(`${searchApi}?q=${encodeURIComponent(value)}`);
                if (!res.ok) throw new Error("Arama başarısız oldu");
                const data = await res.json();
                setResults(data.results || []);
            } catch (err) {
                setError("Arama sırasında hata oluştu.");
                setResults([]);
            } finally {
                setLoading(false);
            }
        }, 300);
    };

    // Seçim
    const handleSelect = (item: any) => {
        onSelect(item);
        setSearch(item[labelKey]);
        setResults([]);
    };

    // Yeni ekle modalı aç
    const handleCreateNew = async () => {
        if (onCreateNew && newValue.length > 1) {
            const created = await onCreateNew(newValue);
            if (created) {
                onSelect(created);
                setSearch(created[labelKey]);
                setShowModal(false);
                setNewValue("");
            }
        }
    };

    return (
        <div className="relative">
            <Input
                value={search}
                onChange={e => handleSearch(e.target.value)}
                placeholder={placeholder}
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
                                search === item[labelKey] && "bg-gray-100"
                            )}
                            onClick={() => handleSelect(item)}
                            tabIndex={0}
                            aria-label={`Seç: ${item[labelKey]}`}
                            onKeyDown={(e) => e.key === "Enter" && handleSelect(item)}
                        >
                            {item[labelKey]}
                        </div>
                    ))}
                </div>
            )}
            {search.length > 1 && results.length === 0 && onCreateNew && (
                <div className="absolute z-10 w-full bg-white border rounded shadow mt-1">
                    <div
                        className="px-4 py-2 cursor-pointer hover:bg-[#7FFFD4] text-sm text-black"
                        onClick={() => setShowModal(true)}
                        tabIndex={0}
                        aria-label="Yeni ekle"
                        onKeyDown={(e) => e.key === "Enter" && setShowModal(true)}
                    >
                        + Yeni Ekle: <span className="font-semibold">{search}</span>
                    </div>
                </div>
            )}
            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
                    <div className="bg-white rounded shadow-lg p-6 w-full max-w-sm">
                        <h2 className="text-lg font-bold mb-4">Yeni Ekle</h2>
                        <Input
                            value={newValue}
                            onChange={e => setNewValue(e.target.value)}
                            placeholder="Yeni değer girin"
                            autoFocus
                        />
                        <div className="flex justify-end gap-2 mt-4">
                            <Button variant="outline" type="button" onClick={() => setShowModal(false)}>
                                Vazgeç
                            </Button>
                            <Button type="button" onClick={handleCreateNew}>
                                Kaydet
                            </Button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
