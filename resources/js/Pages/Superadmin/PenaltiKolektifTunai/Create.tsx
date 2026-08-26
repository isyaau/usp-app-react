import KolektifCreate from '../AngsuranKolektif/Create';

interface Props {
    kelompoks: { id: number; nama: string }[];
    kantors: { id: number; kode: string; nama_kantor: string }[];
}

export default function Create(props: Props) {
    return <KolektifCreate {...props} jenis="penalti" metode="tunai" />;
}