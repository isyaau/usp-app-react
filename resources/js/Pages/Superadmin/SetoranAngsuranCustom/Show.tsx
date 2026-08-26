import KolektifShow from '../AngsuranKolektif/Show';

interface Props {
    transaksi: any;
}

export default function Show(props: Props) {
    return <KolektifShow {...props} />;
}