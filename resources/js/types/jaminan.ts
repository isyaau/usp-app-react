/* ============================================================
   Tipe Jaminan
   ============================================================ */

export interface JaminanDetailRow {
    id?: number;
    detail: string;
}

export interface JaminanRow {
    id: number;
    nama: string;
    details?: JaminanDetailRow[];
}
