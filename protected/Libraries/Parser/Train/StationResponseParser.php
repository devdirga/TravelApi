<?php

namespace Travel\Libraries\Parser\Train;

use Travel\Libraries\Parser\BaseResponseParser;
use Travel\Libraries\Parser\ResponseParser;
use Travel\Libraries\Message\TrainMessage;
use Travel\Libraries\APIController;
use Phalcon\Db;

class StationResponseParser extends BaseResponseParser implements ResponseParser
{
    /**
     * Train message response from core.
     * 
     * @var TrainMessage
     */
    protected $message;

    public function into(APIController $apiController)
    {
        $stationsRaw = array(
            "AGO" => "ARGOPURO",
            "AJ" => "ARJASA",
            "AK" => "ANGKE",
            "AKB" => "AEKLOBA",
            "AW" => "AWIPARI",
            "AWN" => "ARJAWINANGUN",
            "BAP" => "BANDARKALIPAH",
            "BAT" => "BARAT",
            "BB" => "BREBES",
            "BBA" => "BLAMBANGANPAGAR",
            "BBD" => "BABADAN",
            "BBG" => "BRUMBUNG",
            "BBK" => "BABAKAN",
            "BBN" => "BRAMBANAN",
            "BBT" => "BABAT",
            "BBU" => "BLAMBANGANUMPU",
            "BD" => "BANDUNG",
            "BDT" => "BANDARTINGGI",
            "BDW" => "BANGODUWA",
            "BG" => "BANGIL",
            "BGM" => "BUNGAMAS",
            "BGR" => "BAGOR",
            "BH" => "BOHARAN",
            "BIB" => "BLIMBINGPENDOPO",
            "BIJ" => "BINJAI",
            "BJ" => "BOJONEGORO",
            "BJG" => "BOJONG",
            "BJI" => "BANJARSARI",
            "BJL" => "BAJALINGGEI",
            "BJR" => "BANJAR",
            "BKA" => "BULAKAMBA",
            "BKI" => "BEKRI",
            "BKS" => "BEKASI",
            "BL" => "BLITAR",
            "BMA" => "BUMIAYU",
            "BMB" => "BAMBAN",
            "BMG" => "BLIMBING",
            "BNW" => "BENOWO",
            "BOO" => "BOGOR",
            "BRN" => "BARON",
            "BTA" => "BATURAJA",
            "BTG" => "BATANG",
            "BTK" => "BATANGKUIS",
            "BTT" => "BATU TULIS",
            "BW" => "BANYUWANGIBARU",
            "BWO" => "BOWERNO",
            "CA" => "CIGANEA",
            "CAW" => "CIAWI",
            "CB" => "CIBATU",
            "CBD" => "CIBADAK",
            "CBR" => "CIBUNGUR",
            "CCL" => "CICALENGKA",
            "CCR" => "CICURUG",
            "CCY" => "CICAYUR",
            "CD" => "CIKADONGDONG",
            "CG" => "CISOMANG",
            "CGB" => "CIGOMBONG",
            "CI" => "CIAMIS",
            "CIR" => "CIROYOM",
            "CJT" => "CILEJIT",
            "CKL" => "CIKEUSAL",
            "CKP" => "CIKAMPEK",
            "CKR" => "CIKARANG",
            "CLD" => "CILEDUG",
            "CLE" => "CILAME",
            "CLG" => "CILEGON",
            "CLH" => "CILEGEH",
            "CMD" => "CIMINDI",
            "CME" => "CERME",
            "CMI" => "CIMAHI",
            "CMK" => "CIMEKAR",
            "CN" => "CIREBON",
            "CNP" => "CIREBONPRUJAKAN",
            "CO" => "COMAL",
            "CP" => "CILACAP",
            "CPD" => "CIPEUNDEUY",
            "CPI" => "CIPARI",
            "CRA" => "CIPUNEGARA",
            "CRB" => "CARUBAN",
            "CRM" => "CURAHMALANG",
            "CSA" => "CISAAT",
            "CSK" => "CISAUK",
            "CT" => "CATANG",
            "CTH" => "CIKUDAPATEUH",
            "CTR" => "CITERAS",
            "CU" => "CEPU",
            "DAR" => "DARU",
            "DD" => "DUDUK",
            "DEN" => "DENPASAR",
            "DMR" => "DOLOKMERANGIR",
            "DPL" => "DOPLANG",
            "DU" => "DURI",
            "DUK" => "DUKU",
            "DWN" => "DAWUAN",
            "GB" => "GOMBONG",
            "GD" => "GUNDIH",
            "GDB" => "GEDEBAGE",
            "GDG" => "GEDANGAN",
            "GDM" => "GANDRUNGMANGUN",
            "GEB" => "GEMBONG",
            "GG" => "GENENG",
            "GHM" => "GIHAM",
            "GI" => "GRATI",
            "GK" => "GADOBANGKONG",
            "GLM" => "GLENMORE",
            "GM" => "GUMILIR",
            "GMR" => "GAMBIR",
            "GNM" => "GUNUNGMEGANG",
            "GRM" => "GARUM",
            "GRN" => "GARAHAN",
            "HGL" => "HAURGEULIS",
            "HJP" => "HAJIPEMANGGILAN",
            "HL" => "HENGELO",
            "HRP" => "HAURPUGUR",
            "IJ" => "IJO",
            "JAKK" => "JAKARTA KOTA",
            "JBN" => "JAMBON",
            "JBU" => "JAMBU BARU",
            "JG" => "JOMBANG",
            "JN" => "JENAR",
            "JNG" => "JATINEGARA",
            "JR" => "JEMBER",
            "JRL" => "JERUKLEGI",
            "JTB" => "JATIBARANG",
            "JTR" => "JATIROTO",
            "KA" => "KARANGANYAR",
            "KAB" => "KADOKANGANGABUS",
            "KAC" => "KIARACONDONG",
            "KAG" => "KALIBALANGAN",
            "KB" => "KOTABUMI",
            "KBD" => "KALIBODRI",
            "KBR" => "KALIBARU",
            "KBS" => "KEBASEN",
            "KBY" => "KEBAYORAN",
            "KD" => "KEDIRI",
            "KDA" => "KANDANGAN",
            "KDB" => "KEDUNGBANTENG",
            "KDH" => "KEDUNGGEDEH",
            "KDN" => "KEDINDING",
            "KE" => "KARANG TENGAH",
            "KEJ" => "KEDUNGJATI",
            "KEN" => "KRENCENG",
            "KG" => "KEDUNGGALAR",
            "KGB" => "KETANGGUNGAN BARAT",
            "KGG" => "KETANGGUNGAN",
            "KGT" => "KARANGJATI",
            "KI" => "KURAITAJI",
            "KIS" => "KISARAN",
            "KIT" => "KALITIDU",
            "KJ" => "KEMRANJEN",
            "KK" => "KLAKAH",
            "KLI" => "KLARI",
            "KLN" => "KALIWUNGU",
            "KLT" => "KALISAT",
            "KM" => "KEBUMEN",
            "KMO" => "KEMAYORAN",
            "KMR" => "KEMIRI",
            "KNE" => "KARANGASEM",
            "KNN" => "KRADENAN",
            "KNS" => "KRENGSENG",
            "KOP" => "KOTAPADANG",
            "KOS" => "KOSAMBI",
            "KPB" => "KAMPUNG BANDAN",
            "KPN" => "KEPANJEN",
            "KPS" => "KAPAS",
            "KPT" => "KERTAPATI",
            "KRA" => "KARANGANTU",
            "KRAI" => "KARANG SARI",
            "KRN" => "KRIAN",
            "KRO" => "KEBONROMO",
            "KRR" => "KARANGSARI",
            "KRS" => "KRAS",
            "KRT" => "KRETEK",
            "KRW" => "KARANGSUWUNG",
            "KSB" => "KESAMBEN",
            "KSL" => "KALISETAIL",
            "KT" => "KLATEN",
            "KTA" => "KUTOARJO",
            "KTK" => "KOTOK",
            "KTM" => "KERTASEMAYA",
            "KTP" => "KETAPANG",
            "KTS" => "KERTOSONO",
            "KW" => "KARAWANG",
            "KWG" => "KAWUNGANTEN",
            "KWN" => "KUTOWINANGUN",
            "KYA" => "KROYA",
            "LA" => "LUBUK ALUNG",
            "LAR" => "LABUANRATU",
            "LBG" => "LEBENG",
            "LBJ" => "LEBAKJERO",
            "LBP" => "LUBUKPAKAM",
            "LDO" => "LEDOKOMBO",
            "LEC" => "LECES",
            "LG" => "LINGGAPURA",
            "LL" => "LELES",
            "LLG" => "LUBUK LINGGAU",
            "LMB" => "LEMAHABANG",
            "LMG" => "LAMONGAN",
            "LMP" => "LIMAPULUH",
            "LN" => "LANGEN",
            "LO" => "LEUWI GOONG",
            "LOS" => "LOSARI",
            "LPN" => "LEMPUYANGAN",
            "LR" => "LARANGAN",
            "LRA" => "LARANGAN",
            "LRM" => "LUBUKRUKAM",
            "LT" => "LAHAT",
            "LW" => "LAWANG",
            "LWG" => "LUWUNG",
            "MA" => "MAOS",
            "MBM" => "MEMBANGMUDA",
            "MBU" => "MERBAU",
            "MDN" => "MEDAN",
            "ME" => "MUARA ENIM",
            "MER" => "MERAK",
            "MGN" => "MINGGIRAN",
            "MGW" => "MAGUWO",
            "MJ" => "MAJA",
            "ML" => "MALANG",
            "MLK" => "MALANG KOTA LAMA",
            "MLS" => "MALASAN",
            "MLW" => "MELUWUNG",
            "MN" => "MADIUN",
            "MNJ" => "MANONJAYA",
            "MP" => "MARTAPURA",
            "MR" => "MOJOKERTO",
            "MRI" => "MANGGARAI",
            "MRW" => "MRAWAN",
            "MSG" => "MASENG",
            "MSI" => "MASWATI",
            "MSL" => "MUARASALING",
            "MSR" => "MASARAN",
            "NB" => "NGEBRUK",
            "NBO" => "NGROMBO",
            "NDL" => "NGADILUWIH",
            "NG" => "NAGREG",
            "NGN" => "NEGERIAGUNG",
            "NJ" => "NGANJUK",
            "NRR" => "NEGARARATU",
            "NT" => "NGUNUT",
            "NTG" => "NOTOG",
            "PA" => "PARON",
            "PAK" => "PAUHKAMBAR",
            "PAT" => "PATUGURAN",
            "PB" => "PROBOLINGGO",
            "PBA" => "PERBAUNGAN",
            "PBM" => "PRABUMULIH",
            "PC" => "PUCUK",
            "PD" => "PADANG",
            "PDJ" => "PONDOK RANJI",
            "PDL" => "PADALARANG",
            "PGB" => "PEGADENBARU",
            "PGG" => "PAGERGUNUNG",
            "PGJ" => "POGAJIH",
            "PHA" => "PADANGHALABAN",
            "PK" => "PEKALONGAN",
            "PLB" => "PLABUAN",
            "PLD" => "PLERED",
            "PLM" => "PALMERAH",
            "PME" => "PAMINGKE",
            "PML" => "PEMALANG",
            "PMN" => "PARIAMAN",
            "PNL" => "PANUNGGALAN",
            "PNW" => "PANINJAWAN",
            "PPK" => "PRUPUK",
            "PPR" => "PAPAR",
            "PR" => "PORONG",
            "PRA" => "PERLANAAN",
            "PRB" => "PREMBUN",
            "PRK" => "PARUNG KUDA",
            "PRP" => "PARUNG PANJANG",
            "PS" => "PASURUAN",
            "PSE" => "PASAR SENEN",
            "PSI" => "PAKISAJI",
            "PTA" => "PETARUKAN",
            "PTR" => "PETERONGAN",
            "PUR" => "PULURAJA",
            "PWK" => "PURWAKARTA",
            "PWS" => "PURWOSARI",
            "PWT" => "PURWOKERTO",
            "RAP" => "RANTAU PRAPAT",
            "RBG" => "RANDUBLATUNG",
            "RBP" => "RAMBIPUJI",
            "RCK" => "RANCAEKEK",
            "RDA" => "RANDUAGUNG",
            "RDN" => "RANDEGAN",
            "RGP" => "ROGOJAMPI",
            "RGS" => "RENGAS",
            "RH" => "RENDEH",
            "RJ" => "REJOTANGAN",
            "RJP" => "RAJAPOLAH",
            "RJS" => "REJOSARI",
            "RK" => "RANGKASBITUNG",
            "RPH" => "RAMPAH",
            "SAD" => "SADANG",
            "SB" => "SURABAYA KOTA",
            "SBI" => "SURABAYA PASAR TURI",
            "SBJ" => "SEI BEJANGKAR",
            "SBL" => "SUMBERGEMPOL",
            "SBO" => "SUMOBITO",
            "SBP" => "SUMBERPUCUNG",
            "SDA" => "SIDOARJO",
            "SDM" => "SUDIMARA",
            "SDR" => "SIDAREJA",
            "SDU" => "SINDANGLAUT",
            "SG" => "SERANG",
            "SGG" => "SONGGOM",
            "SGJ" => "SINGOJURUH",
            "SGS" => "SINGOSARI",
            "SGU" => "SURABAYA GUBENG",
            "SGUX" => "SURABAYA GUBENGX",
            "SI" => "SUKABUMI",
            "SIR" => "SIANTAR",
            "SK" => "SOLOJEBRES",
            "SKJ" => "SUKOREJO",
            "SKP" => "SIKAMPUH",
            "SKT" => "SASAKSAAT",
            "SLO" => "SOLOBALAPAN",
            "SLR" => "SUMLARAN",
            "SLS" => "SULUSUBAN",
            "SMB" => "SEMBUNG",
            "SMC" => "SEMARANGPONCOL",
            "SMT" => "SEMARANGTAWANG",
            "SNA" => "SAUNGNAGA",
            "SPH" => "SUMPIUH",
            "SPJ" => "SEPANJANG",
            "SPL" => "SEMPOLAN",
            "SR" => "SRAGEN",
            "SRD" => "SARADAN",
            "SRI" => "SRAGI",
            "SRJ" => "SUMBERREJO",
            "SRP" => "SERPONG",
            "SRW" => "SRUWENG",
            "STL" => "SENTOLO",
            "SUT" => "SUKATANI",
            "SWD" => "SUBERWADUNG",
            "SWT" => "SROWOT",
            "TA" => "TULUNGAGUNG",
            "TAB" => "TABING",
            "TAL" => "TALUN",
            "TB" => "TAMBUN",
            "TBI" => "TEBING TINGGI",
            "TBK" => "TAMBAK",
            "TEJ" => "TENJO",
            "TES" => "TANDES",
            "TG" => "TEGAL",
            "TGA" => "TANGGULANGIN",
            "TGI" => "TEGINENENG",
            "TGL" => "TANGGUL",
            "TGN" => "TANJUNG",
            "TGR" => "TEMUGURUH",
            "TGS" => "TIGARAKSA",
            "THB" => "TANAH ABANG",
            "TI" => "TEBINGTINGGI",
            "TIG" => "TIGARAKSA",
            "TIS" => "TERISI",
            "TJS" => "TANJUNGRASA",
            "TLY" => "TULUNGBUYUT",
            "TNB" => "TANJUNGBALAI",
            "TNG" => "TANGERANG",
            "TNK" => "TANJUNGKARANG",
            "TOJB" => "TONJONG BARU",
            "TPK" => "TANJUNG PRIUK",
            "TRK" => "TARIK",
            "TSM" => "TASIKMALAYA",
            "TW" => "TELAWA",
            "UJM" => "UJANMAS",
            "UJN" => "UJUNGNEGORO",
            "WAY" => "WAYTUBA",
            "WB" => "WARUNG BANDREK",
            "WDU" => "WADU",
            "WDW" => "WARUDUWUR",
            "WG" => "WLINGI",
            "WIL" => "WILANGAN-INVALID",
            "WK" => "WALIKUKUN",
            "WLG" => "WILANGAN",
            "WLR" => "WELERI",
            "WLT" => "WALANTAKA",
            "WN" => "WONOKERTO",
            "WNS" => "WONOSARI",
            "WO" => "WONOKROMO",
            "WOX" => "WONOKROMOX",
            "WR" => "WARU",
            "WT" => "WATES",
            "YK" => "YOGYAKARTA"
        );

        $stations = array();

        $query_getdatastation = $apiController->db->query("select * from data_stasiun_kereta_api where nama_kota is not null AND nama_kota<>'-' 
            union 
            select * from data_stasiun_kereta_api where nama_kota is null or nama_kota = '-'  order by  nama_kota  asc NULLS LAST");

        $query_getdatastation->setFetchMode(Db::FETCH_OBJ);

        $data = $query_getdatastation->fetchAll();
        $data_o =  array();
        $idx = 0;

        foreach ($data as $value) {
            $data[$idx] = array(
                "id_stasiun" => $value->id_stasiun,
                "nama_stasiun" => $value->nama_stasiun,
                "nama_kota" => ($value->nama_kota === "-" || !$value->nama_kota) ? "unknown" : $value->nama_kota,
                "is_active" => $value->is_active,

            );
            $idx++;
        }

        $apiController->response->data = $data;
    }
}