import KolektifEdit from '../AngsuranKolektif/Edit';

interface Props {
    transaksi: any;
    kantors: { id: number; nama_kantor: string }[];
}

export default function Edit(props: Props) {
    return <KolektifEdit {...props} />;
}