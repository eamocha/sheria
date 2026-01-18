<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_14 extends CI_Controller
{

    use MigrationLogTrait;

    public $log_path = null;
    public $countries_arabic_list = [
        [
            'countryCode' => 'AD',
            'name' => 'أندورا',
        ],
        [
            'countryCode' => 'AE',
            'name' => 'الإمارات العربية المتحدة',
        ],
        [
            'countryCode' => 'AF',
            'name' => 'أفغانستان',
        ],
        [
            'countryCode' => 'AG',
            'name' => 'أنتيغوا و باربودا',
        ],
        [
            'countryCode' => 'AI',
            'name' => 'أنغويلا',
        ],
        [
            'countryCode' => 'AL',
            'name' => 'ألبانيا',
        ],
        [
            'countryCode' => 'AM',
            'name' => 'أرمينيا',
        ],
        [
            'countryCode' => 'AO',
            'name' => 'أنغولا',
        ],
        [
            'countryCode' => 'AQ',
            'name' => 'أنتاركتيكا',
        ],
        [
            'countryCode' => 'AR',
            'name' => 'الأرجنتين',
        ],
        [
            'countryCode' => 'AS',
            'name' => 'ساموا الأمريكية',
        ],
        [
            'countryCode' => 'AT',
            'name' => 'النمسا',
        ],
        [
            'countryCode' => 'AU',
            'name' => 'أستراليا',
        ],
        [
            'countryCode' => 'AW',
            'name' => 'أروبا',
        ],
        [
            'countryCode' => 'AX',
            'name' => 'جزر أولاند',
        ],
        [
            'countryCode' => 'AZ',
            'name' => 'أذربيجان',
        ],
        [
            'countryCode' => 'BA',
            'name' => 'البوسنة و الهرسك',
        ],
        [
            'countryCode' => 'BB',
            'name' => 'باربادوس',
        ],
        [
            'countryCode' => 'BD',
            'name' => 'بنغلاديش',
        ],
        [
            'countryCode' => 'BE',
            'name' => 'بلجيكا',
        ],
        [
            'countryCode' => 'BF',
            'name' => 'بوركينا فاسو',
        ],
        [
            'countryCode' => 'BG',
            'name' => 'بلغاريا',
        ],
        [
            'countryCode' => 'BH',
            'name' => 'البحرين',
        ],
        [
            'countryCode' => 'BI',
            'name' => 'بروندي',
        ],
        [
            'countryCode' => 'BJ',
            'name' => 'بنيين',
        ],
        [
            'countryCode' => 'BL',
            'name' => 'سان بارتيلمي',
        ],
        [
            'countryCode' => 'BM',
            'name' => 'برمودا',
        ],
        [
            'countryCode' => 'BN',
            'name' => 'بروناي',
        ],
        [
            'countryCode' => 'BO',
            'name' => 'بوليفيا',
        ],
        [
            'countryCode' => 'BQ',
            'name' => 'بونير',
        ],
        [
            'countryCode' => 'BR',
            'name' => 'البرازيل',
        ],
        [
            'countryCode' => 'BS',
            'name' => 'جزر البهاما',
        ],
        [
            'countryCode' => 'BT',
            'name' => 'بوتان',
        ],
        [
            'countryCode' => 'BV',
            'name' => 'جزيرة بوفيه',
        ],
        [
            'countryCode' => 'BW',
            'name' => 'بوتسوانا',
        ],
        [
            'countryCode' => 'BY',
            'name' => 'بيلاروسيا',
        ],
        [
            'countryCode' => 'BZ',
            'name' => 'بليز',
        ],
        [
            'countryCode' => 'CA',
            'name' => 'كندا',
        ],
        [
            'countryCode' => 'CC',
            'name' => 'جزر كوكوس',
        ],
        [
            'countryCode' => 'CD',
            'name' => 'الكونغو الديموقراطية',
        ],
        [
            'countryCode' => 'CF',
            'name' => 'جمهورية أفريقيا الوسطى',
        ],
        [
            'countryCode' => 'CG',
            'name' => 'الكونغو',
        ],
        [
            'countryCode' => 'CH',
            'name' => 'سويسرا',
        ],
        [
            'countryCode' => 'CI',
            'name' => 'ساحل العاج',
        ],
        [
            'countryCode' => 'CK',
            'name' => 'جزر كوك',
        ],
        [
            'countryCode' => 'CL',
            'name' => 'تشيلي',
        ],
        [
            'countryCode' => 'CM',
            'name' => 'الكاميرون',
        ],
        [
            'countryCode' => 'CN',
            'name' => 'الصين',
        ],
        [
            'countryCode' => 'CO',
            'name' => 'كولومبيا',
        ],
        [
            'countryCode' => 'CR',
            'name' => 'كوستاريكا',
        ],
        [
            'countryCode' => 'CU',
            'name' => 'كوبا',
        ],
        [
            'countryCode' => 'CV',
            'name' => 'الرأس الأخضر',
        ],
        [
            'countryCode' => 'CW',
            'name' => 'كوراساو',
        ],
        [
            'countryCode' => 'CX',
            'name' => 'جزيرة كريسماس',
        ],
        [
            'countryCode' => 'CY',
            'name' => 'قبرص',
        ],
        [
            'countryCode' => 'CZ',
            'name' => 'التشيك',
        ],
        [
            'countryCode' => 'DE',
            'name' => 'ألمانيا',
        ],
        [
            'countryCode' => 'DJ',
            'name' => 'جيبوتي',
        ],
        [
            'countryCode' => 'DK',
            'name' => 'الدنمارك',
        ],
        [
            'countryCode' => 'DM',
            'name' => 'دومينيكا',
        ],
        [
            'countryCode' => 'DO',
            'name' => 'جمهورية الدومينيكان',
        ],
        [
            'countryCode' => 'DZ',
            'name' => 'الجزائر',
        ],
        [
            'countryCode' => 'EC',
            'name' => 'الإكوادور',
        ],
        [
            'countryCode' => 'EE',
            'name' => 'إستونيا',
        ],
        [
            'countryCode' => 'EG',
            'name' => 'مصر',
        ],
        [
            'countryCode' => 'EH',
            'name' => 'الصحراء الغربية',
        ],
        [
            'countryCode' => 'ER',
            'name' => 'إريتريا',
        ],
        [
            'countryCode' => 'ES',
            'name' => 'أسبانيا',
        ],
        [
            'countryCode' => 'ET',
            'name' => 'أثيوبيا',
        ],
        [
            'countryCode' => 'FI',
            'name' => 'فنلندا',
        ],
        [
            'countryCode' => 'FJ',
            'name' => 'فيجي',
        ],
        [
            'countryCode' => 'FK',
            'name' => 'جزر فوكلاند',
        ],
        [
            'countryCode' => 'FM',
            'name' => 'ميكرونيسيا',
        ],
        [
            'countryCode' => 'FO',
            'name' => 'جزر فارو',
        ],
        [
            'countryCode' => 'FR',
            'name' => 'فرنسا',
        ],
        [
            'countryCode' => 'GA',
            'name' => 'الغابون',
        ],
        [
            'countryCode' => 'GB',
            'name' => 'المملكة المتحدة',
        ],
        [
            'countryCode' => 'GD',
            'name' => 'غرينادا',
        ],
        [
            'countryCode' => 'GE',
            'name' => 'جورجيا',
        ],
        [
            'countryCode' => 'GF',
            'name' => 'غينيا الفرنسية',
        ],
        [
            'countryCode' => 'GG',
            'name' => 'غيرنزي',
        ],
        [
            'countryCode' => 'GH',
            'name' => 'غانا',
        ],
        [
            'countryCode' => 'GI',
            'name' => 'جبل طارق',
        ],
        [
            'countryCode' => 'GL',
            'name' => 'غرينلاند',
        ],
        [
            'countryCode' => 'GM',
            'name' => 'جامبيا',
        ],
        [
            'countryCode' => 'GN',
            'name' => 'غينيا',
        ],
        [
            'countryCode' => 'GP',
            'name' => 'جزر غوادلوب',
        ],
        [
            'countryCode' => 'GQ',
            'name' => 'غينيا الاستوائية',
        ],
        [
            'countryCode' => 'GR',
            'name' => 'اليونان',
        ],
        [
            'countryCode' => 'GS',
            'name' => 'جورجيا الجنوبية وجزر ساندويتش الجنوبية',
        ],
        [
            'countryCode' => 'GT',
            'name' => 'غواتيمالا',
        ],
        [
            'countryCode' => 'GU',
            'name' => 'غوام',
        ],
        [
            'countryCode' => 'GW',
            'name' => 'غينيا بيساو',
        ],
        [
            'countryCode' => 'GY',
            'name' => 'غيانا',
        ],
        [
            'countryCode' => 'HK',
            'name' => 'هونغ كونغ',
        ],
        [
            'countryCode' => 'HM',
            'name' => 'جزيرة هيرد وجزر ماكدونالد',
        ],
        [
            'countryCode' => 'HN',
            'name' => 'هندوراس',
        ],
        [
            'countryCode' => 'HR',
            'name' => 'كرواتيا',
        ],
        [
            'countryCode' => 'HT',
            'name' => 'هايتي',
        ],
        [
            'countryCode' => 'HU',
            'name' => 'المجر',
        ],
        [
            'countryCode' => 'ID',
            'name' => 'أندونيسيا',
        ],
        [
            'countryCode' => 'IE',
            'name' => 'إيرلندا',
        ],
        [
            'countryCode' => 'IM',
            'name' => 'جزيرة مان',
        ],
        [
            'countryCode' => 'IN',
            'name' => 'الهند',
        ],
        [
            'countryCode' => 'IO',
            'name' => 'إقليم المحيط الهندي البريطاني',
        ],
        [
            'countryCode' => 'IQ',
            'name' => 'العراق',
        ],
        [
            'countryCode' => 'IR',
            'name' => 'إيران',
        ],
        [
            'countryCode' => 'IS',
            'name' => 'أيسلندا',
        ],
        [
            'countryCode' => 'IT',
            'name' => 'إيطاليا',
        ],
        [
            'countryCode' => 'JE',
            'name' => 'جيرزي',
        ],
        [
            'countryCode' => 'JM',
            'name' => 'جاميكا',
        ],
        [
            'countryCode' => 'JO',
            'name' => 'الأردن',
        ],
        [
            'countryCode' => 'JP',
            'name' => 'اليابان',
        ],
        [
            'countryCode' => 'KE',
            'name' => 'كينيا',
        ],
        [
            'countryCode' => 'KG',
            'name' => 'قيرغيزستان',
        ],
        [
            'countryCode' => 'KH',
            'name' => 'كمبوديا',
        ],
        [
            'countryCode' => 'KI',
            'name' => 'كيريباتي',
        ],
        [
            'countryCode' => 'KM',
            'name' => 'جزر القمر',
        ],
        [
            'countryCode' => 'KN',
            'name' => 'سانت كيتس و نيفيس',
        ],
        [
            'countryCode' => 'KP',
            'name' => 'كوريا الشمالية',
        ],
        [
            'countryCode' => 'KR',
            'name' => 'كوريا الجنوبية',
        ],
        [
            'countryCode' => 'KW',
            'name' => 'الكويت',
        ],
        [
            'countryCode' => 'KY',
            'name' => 'جزر كايمان',
        ],
        [
            'countryCode' => 'KZ',
            'name' => 'كازاخستان',
        ],
        [
            'countryCode' => 'LA',
            'name' => 'لاوس',
        ],
        [
            'countryCode' => 'LB',
            'name' => 'لبنان',
        ],
        [
            'countryCode' => 'LC',
            'name' => 'سانت لوسيا',
        ],
        [
            'countryCode' => 'LI',
            'name' => 'ليختنشتاين',
        ],
        [
            'countryCode' => 'LK',
            'name' => 'سريلانكا',
        ],
        [
            'countryCode' => 'LR',
            'name' => 'ليبيريا',
        ],
        [
            'countryCode' => 'LS',
            'name' => 'ليسوتو',
        ],
        [
            'countryCode' => 'LT',
            'name' => 'ليتوانيا',
        ],
        [
            'countryCode' => 'LU',
            'name' => 'لوكسمبورغ',
        ],
        [
            'countryCode' => 'LV',
            'name' => 'لاتفيا',
        ],
        [
            'countryCode' => 'LY',
            'name' => 'ليبيا',
        ],
        [
            'countryCode' => 'MA',
            'name' => 'المغرب',
        ],
        [
            'countryCode' => 'MC',
            'name' => 'موناكو',
        ],
        [
            'countryCode' => 'MD',
            'name' => 'مولدوفا',
        ],
        [
            'countryCode' => 'ME',
            'name' => 'الجبل الأسود',
        ],
        [
            'countryCode' => 'MF',
            'name' => 'سان مارتن',
        ],
        [
            'countryCode' => 'MG',
            'name' => 'مدغشقر',
        ],
        [
            'countryCode' => 'MH',
            'name' => 'جزر مارشال',
        ],
        [
            'countryCode' => 'MK',
            'name' => 'مقدونيا',
        ],
        [
            'countryCode' => 'ML',
            'name' => 'مالي',
        ],
        [
            'countryCode' => 'MM',
            'name' => 'مينمار',
        ],
        [
            'countryCode' => 'MN',
            'name' => 'منغوليا',
        ],
        [
            'countryCode' => 'MO',
            'name' => 'ماكاو',
        ],
        [
            'countryCode' => 'MP',
            'name' => 'جزر ماريانا الشمالية',
        ],
        [
            'countryCode' => 'MQ',
            'name' => 'مارتينيك',
        ],
        [
            'countryCode' => 'MR',
            'name' => 'موريتانيا',
        ],
        [
            'countryCode' => 'MS',
            'name' => 'مونتسيرات',
        ],
        [
            'countryCode' => 'MT',
            'name' => 'مالطا',
        ],
        [
            'countryCode' => 'MU',
            'name' => 'جمهورية موريشيوس',
        ],
        [
            'countryCode' => 'MV',
            'name' => 'جزر المالديف',
        ],
        [
            'countryCode' => 'MW',
            'name' => 'مالاوي',
        ],
        [
            'countryCode' => 'MX',
            'name' => 'المكسيك',
        ],
        [
            'countryCode' => 'MY',
            'name' => 'ماليزيا',
        ],
        [
            'countryCode' => 'MZ',
            'name' => 'الموزمبيق',
        ],
        [
            'countryCode' => 'NA',
            'name' => 'نامبيا',
        ],
        [
            'countryCode' => 'NC',
            'name' => 'كاليدونيا الجديدة',
        ],
        [
            'countryCode' => 'NE',
            'name' => 'النيجر',
        ],
        [
            'countryCode' => 'NF',
            'name' => 'جزيرة نورفولك',
        ],
        [
            'countryCode' => 'NG',
            'name' => 'نيجيريا',
        ],
        [
            'countryCode' => 'NI',
            'name' => 'نيكاراغوا',
        ],
        [
            'countryCode' => 'NL',
            'name' => 'هولندا',
        ],
        [
            'countryCode' => 'NO',
            'name' => 'النروج',
        ],
        [
            'countryCode' => 'NP',
            'name' => 'نيبال',
        ],
        [
            'countryCode' => 'NR',
            'name' => 'ناورو',
        ],
        [
            'countryCode' => 'NU',
            'name' => 'نييوي',
        ],
        [
            'countryCode' => 'NZ',
            'name' => 'نيوزيلندا',
        ],
        [
            'countryCode' => 'OM',
            'name' => 'عمان',
        ],
        [
            'countryCode' => 'PA',
            'name' => 'بنما',
        ],
        [
            'countryCode' => 'PE',
            'name' => 'البيرو',
        ],
        [
            'countryCode' => 'PF',
            'name' => 'بولينزيا الفرنسية',
        ],
        [
            'countryCode' => 'PG',
            'name' => 'بابوا غينيا الجديدة',
        ],
        [
            'countryCode' => 'PH',
            'name' => 'الفليبين',
        ],
        [
            'countryCode' => 'PK',
            'name' => 'باكستان',
        ],
        [
            'countryCode' => 'PL',
            'name' => 'بولندا',
        ],
        [
            'countryCode' => 'PM',
            'name' => 'سان بيير و ميكلون',
        ],
        [
            'countryCode' => 'PN',
            'name' => 'جزر بيتكيرن',
        ],
        [
            'countryCode' => 'PR',
            'name' => 'بورتوريكو',
        ],
        [
            'countryCode' => 'PS',
            'name' => 'فلسطين',
        ],
        [
            'countryCode' => 'PT',
            'name' => 'البرتغال',
        ],
        [
            'countryCode' => 'PW',
            'name' => 'بالاو',
        ],
        [
            'countryCode' => 'PY',
            'name' => 'الباراغواي',
        ],
        [
            'countryCode' => 'QA',
            'name' => 'قطر',
        ],
        [
            'countryCode' => 'RE',
            'name' => 'لا ريونيون',
        ],
        [
            'countryCode' => 'RO',
            'name' => 'رومانيا',
        ],
        [
            'countryCode' => 'RS',
            'name' => 'صربيا',
        ],
        [
            'countryCode' => 'RU',
            'name' => 'روسيا',
        ],
        [
            'countryCode' => 'RW',
            'name' => 'رواندا',
        ],
        [
            'countryCode' => 'SA',
            'name' => 'المملكة العربية السعودية',
        ],
        [
            'countryCode' => 'SB',
            'name' => 'جزر سليمان',
        ],
        [
            'countryCode' => 'SC',
            'name' => 'سيشل',
        ],
        [
            'countryCode' => 'SD',
            'name' => 'السودان',
        ],
        [
            'countryCode' => 'SE',
            'name' => 'السويد',
        ],
        [
            'countryCode' => 'SG',
            'name' => 'سنغافورة',
        ],
        [
            'countryCode' => 'SH',
            'name' => 'سانت هيلينا',
        ],
        [
            'countryCode' => 'SI',
            'name' => 'سلوفينيا',
        ],
        [
            'countryCode' => 'SJ',
            'name' => 'سفالبارد و يان ماين',
        ],
        [
            'countryCode' => 'SK',
            'name' => 'سلوفاكيا',
        ],
        [
            'countryCode' => 'SL',
            'name' => 'سيراليون',
        ],
        [
            'countryCode' => 'SM',
            'name' => 'سان مارينو',
        ],
        [
            'countryCode' => 'SN',
            'name' => 'السنغال',
        ],
        [
            'countryCode' => 'SO',
            'name' => 'الصومال',
        ],
        [
            'countryCode' => 'SR',
            'name' => 'سورينام',
        ],
        [
            'countryCode' => 'SS',
            'name' => 'جنوب السودان',
        ],
        [
            'countryCode' => 'ST',
            'name' => 'ساو تومي و برينسيب',
        ],
        [
            'countryCode' => 'SV',
            'name' => 'السلفادور',
        ],
        [
            'countryCode' => 'SX',
            'name' => 'سينت مارتن',
        ],
        [
            'countryCode' => 'SY',
            'name' => 'سوريا',
        ],
        [
            'countryCode' => 'SZ',
            'name' => 'إسواتيني',
        ],
        [
            'countryCode' => 'TC',
            'name' => 'جزر توركس و كايكوس',
        ],
        [
            'countryCode' => 'TD',
            'name' => 'تشاد',
        ],
        [
            'countryCode' => 'TF',
            'name' => 'أراضي فرنسية جنوبية و أنتارتيكية',
        ],
        [
            'countryCode' => 'TG',
            'name' => 'توغو',
        ],
        [
            'countryCode' => 'TH',
            'name' => 'تايلاند',
        ],
        [
            'countryCode' => 'TJ',
            'name' => 'طاجكستان',
        ],
        [
            'countryCode' => 'TK',
            'name' => 'توكيلاو',
        ],
        [
            'countryCode' => 'TL',
            'name' => 'تيمور الشرقية',
        ],
        [
            'countryCode' => 'TM',
            'name' => 'تركمانستان',
        ],
        [
            'countryCode' => 'TN',
            'name' => 'تونس',
        ],
        [
            'countryCode' => 'TO',
            'name' => 'تونغا',
        ],
        [
            'countryCode' => 'TR',
            'name' => 'تركيا',
        ],
        [
            'countryCode' => 'TT',
            'name' => 'ترينيداد و توباغو',
        ],
        [
            'countryCode' => 'TV',
            'name' => 'توفالو',
        ],
        [
            'countryCode' => 'TW',
            'name' => 'تايوان',
        ],
        [
            'countryCode' => 'TZ',
            'name' => 'تانزانيا',
        ],
        [
            'countryCode' => 'UA',
            'name' => 'أوكرانيا',
        ],
        [
            'countryCode' => 'UG',
            'name' => 'أوغندا',
        ],
        [
            'countryCode' => 'UM',
            'name' => 'جزر الولايات المتحدة الصغيرة النائية',
        ],
        [
            'countryCode' => 'US',
            'name' => 'الولايات المتحدة',
        ],
        [
            'countryCode' => 'UY',
            'name' => 'الأوروغواي',
        ],
        [
            'countryCode' => 'UZ',
            'name' => 'أوزباكستان',
        ],
        [
            'countryCode' => 'VA',
            'name' => 'الفاتيكان',
        ],
        [
            'countryCode' => 'VC',
            'name' => 'سانت فينسنت و الغرينادين',
        ],
        [
            'countryCode' => 'VE',
            'name' => 'فنزويلا',
        ],
        [
            'countryCode' => 'VG',
            'name' => 'الجزر العذراء البريطانية',
        ],
        [
            'countryCode' => 'VI',
            'name' => 'الجزر العذراء الأميركية',
        ],
        [
            'countryCode' => 'VN',
            'name' => 'فيتنام',
        ],
        [
            'countryCode' => 'VU',
            'name' => 'فانواتو',
        ],
        [
            'countryCode' => 'WF',
            'name' => 'واليس و فوتونا',
        ],
        [
            'countryCode' => 'WS',
            'name' => 'ساموا',
        ],
        [
            'countryCode' => 'XK',
            'name' => 'كوسوفو',
        ],
        [
            'countryCode' => 'YE',
            'name' => 'اليمن',
        ],
        [
            'countryCode' => 'YT',
            'name' => 'مايوت',
        ],
        [
            'countryCode' => 'ZA',
            'name' => 'جنوب أفريقيا',
        ],
        [
            'countryCode' => 'ZM',
            'name' => 'زامبيا',
        ],
        [
            'countryCode' => 'ZW',
            'name' => 'زمبابواي',
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->migrate_cfv();
        $this->set_countries_arabic_names();
        $this->remove_country_name_field();
        $this->update_saved_filter();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function migrate_cfv()
    {
        $this->write_log($this->log_path, 'Custom fields migration started');
        $sql = 'select custom_field_id, recordId, date_value, time_value, text_value, cf.type
                from custom_field_values cfv
                inner join custom_fields cf on cf.id = cfv.custom_field_id';
        $query_execution = $this->db->query($sql);
        $cfv_details = $query_execution->result_array();
        $this->db->empty_table('custom_field_values');
        foreach ($cfv_details as $key => $value) {
            if (isset($value["text_value"]) && ($value["type"] === 'list' || $value["type"] === 'lookup')) {
                $split_values = explode(',', $value["text_value"]);
                unset($value["type"]);
                foreach ($split_values as $key_record => $record) {
                    $value["text_value"] = $record;
                    $this->db->insert('custom_field_values', $value);
                }
            } else {
                unset($value["type"]);
                $this->db->insert('custom_field_values', $value);
            }
        }
        $this->write_log($this->log_path, 'Custom fields migration ended');
    }

    public function set_countries_arabic_names()
    {
        $this->write_log($this->log_path, 'set_countries_arabic_names started', 'info');

        $this->db->select("id, countryName, countryCode");
        $countries_list_db = $this->db->get("countries");
        $countries_list = $countries_list_db->result();
        $this->db->reset_query();

        foreach ($this->countries_arabic_list as $country_arabic) {
            foreach ($countries_list as $row) {
                if ($country_arabic['countryCode'] == $row->countryCode) {
                    $this->db->insert("countries_languages", [
                        "country_id" => $row->id,
                        "language_id" => 1,
                        "name" => $row->countryName
                    ]);

                    $this->db->insert("countries_languages", [
                        "country_id" => $row->id,
                        "language_id" => 2,
                        "name" => $country_arabic['name']
                    ]);

                    $this->db->insert("countries_languages", [
                        "country_id" => $row->id,
                        "language_id" => 3,
                        "name" => $row->countryName
                    ]);

                    $this->db->insert("countries_languages", [
                        "country_id" => $row->id,
                        "language_id" => 4,
                        "name" => $row->countryName
                    ]);
                }
            }
        }

        $this->write_log($this->log_path, 'set_countries_arabic_names is done', 'info');
    }

    public function remove_country_name_field()
    {
        $this->write_log($this->log_path, 'remove_country_name_field started', 'info');
        
        if (($this->db->dbdriver === 'sqlsrv')) {
            $this->db->query("DECLARE @constraint_name as NVARCHAR(255);
                    DECLARE @constraint_cursor as CURSOR;
                    DECLARE @columns_name TABLE (name varchar(1000));
                    DECLARE @table_name as NVARCHAR(255);
                    SET @table_name = 'countries';
                    INSERT INTO @columns_name VALUES ('countryName');
                    SET @constraint_cursor = CURSOR FOR
                    (SELECT fk.name AS constraint_name
                    FROM sys.foreign_keys fk
                        INNER JOIN sys.foreign_key_columns fkcol on fkcol.constraint_object_id = fk.object_id
                        INNER JOIN sys.columns col on col.column_id = fkcol.parent_column_id and fk.parent_object_id = col.object_id
                    WHERE fk.parent_object_id = OBJECT_ID(@table_name)
                        AND col.name IN (SELECT name FROM @columns_name)
                    UNION
                    SELECT chk.name AS constraint_name
                    FROM sys.check_constraints chk
                        INNER JOIN sys.columns col on col.column_id = chk.parent_column_id  and chk.parent_object_id = col.object_id
                    WHERE chk.parent_object_id = OBJECT_ID(@table_name)
                        AND col.name IN (SELECT name FROM @columns_name)
                    UNION
                    SELECT dc.name AS constraint_name
                    FROM sys.default_constraints dc
                        INNER JOIN sys.columns col ON col.default_object_id = dc.object_id and dc.parent_object_id = col.object_id
                    WHERE dc.parent_object_id = OBJECT_ID(@table_name)
                        AND col.name IN (SELECT name FROM @columns_name));
                    OPEN @constraint_cursor;
                    FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
                    WHILE @@FETCH_STATUS = 0
                    BEGIN
                     EXEC(N'alter table ' + @table_name + ' drop constraint  [' + @constraint_name + N']');
                     FETCH NEXT FROM @constraint_cursor INTO @constraint_name;
                    END
                    CLOSE @constraint_cursor;
                    DEALLOCATE @constraint_cursor;");
        }
        
        $this->db->query("ALTER TABLE countries DROP COLUMN countryName");

        $this->write_log($this->log_path, 'remove_country_name_field is done', 'info');
    }

    private function update_saved_filter()
    {
        $this->load->model('grid_saved_filter', 'grid_saved_filterfactory');
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $all_filters = $this->grid_saved_filter->get_all_filters_by_model('Litigation');
        
        if (isset($all_filters) && is_array($all_filters)) {
            foreach ($all_filters as &$filter) {
                if (!empty($filter['formData'])) {
                    $grid_details = unserialize($filter['formData']);
                    $grid_filters = json_decode($grid_details['gridFilters']);
                    
                    if (isset($grid_filters->filters) && is_array($grid_filters->filters)) {
                        foreach ($grid_filters->filters as &$grid_filter){
                            if (isset($grid_filter->filters) && is_array($grid_filter->filters)) {
                                foreach ($grid_filter as &$single_filters){
                                    if (isset($single_filters) && is_array($single_filters)) {
                                        foreach ($single_filters as &$single_filter) {
                                            $single_filter = (array)$single_filter;

                                            switch ($single_filter['field']) {
                                                case 'opponentNames':
                                                    $single_filter['function'] = 'opponent_names_field_value';
                                                    break;
                                                case 'legal_cases.caseID':
                                                    $single_filter['function'] = 'case_id_field_value';
                                                    break;
                                                case 'legal_cases.assignee':
                                                    $single_filter['function'] = 'assignee_field_value';
                                                    break;
                                                case 'legal_cases.contactContributor':
                                                    $single_filter['function'] = 'contact_contributor_field_value';
                                                    break;
                                                case 'legal_cases.company':
                                                    $single_filter['function'] = 'com.name';
                                                    break;
                                                case 'legal_cases.contact':
                                                    $single_filter['function'] = 'contact_field_value';
                                                    break;
                                                case 'legal_cases.clientType':
                                                    $single_filter['function'] = 'client_type_field_value';
                                                    break;
                                                case 'legal_cases.clientName':
                                                    $single_filter['function'] = 'client_name_field_value';
                                                    break;
                                                case 'legal_cases.referredByName':
                                                    $single_filter['function'] = 'referred_by_name_field_value';
                                                    break;
                                                case 'legal_cases.requestedByName':
                                                    $single_filter['function'] = 'requested_by_name_field_value';
                                                    break;
                                                case 'legal_cases.effectiveEffort':
                                                    $single_filter['function'] = 'lcee.effectiveEffort';
                                                    break;
                                                case 'legal_cases.contactOutsourceTo':
                                                    $single_filter['function'] = 'contact_outsource_to_field_value';
                                                    break;
                                                case 'legal_cases.legalCaseContainerSubject':
                                                    $single_filter['function'] = 'legal_case_containers.subject';
                                                    break;
                                                case 'legal_cases.court_type_id':
                                                    $single_filter['function'] = 'legal_case_litigation_details.court_type_id';
                                                    break;
                                                case 'legal_cases.court_region_id':
                                                    $single_filter['function'] = 'legal_case_litigation_details.court_region_id';
                                                    break;
                                                case 'legal_cases.court_degree_id':
                                                    $single_filter['function'] = 'legal_case_litigation_details.court_degree_id';
                                                    break;
                                                case 'legal_cases.court_id':
                                                    $single_filter['function'] = 'legal_case_litigation_details.court_id';
                                                    break;
                                                case 'legal_cases.isCP':
                                                    $single_filter['function'] = 'is_cp_field_value';
                                                    break;
                                                case 'legal_cases.sentenceDate':
                                                    $single_filter['function'] = 'legal_case_litigation_details.sentenceDate';
                                                    break;
                                                case 'legal_cases.constitutionDate':
                                                    $single_filter['function'] = 'legal_case_litigation_details.constitutionDate';
                                                    break;
                                                case 'legal_cases.litigationExternalRef':
                                                    $single_filter['function'] = 'legal_case_litigation_external_references.number';
                                                    break;
                                                case 'legal_cases.opponentNationalities':
                                                    $single_filter['function'] = 'opponent_nationalities_field_value';
                                                    break;
                                                default:
                                                    break;
                                            }
                                            $single_filter = (object)$single_filter;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $grid_filters = json_encode($grid_filters);
                $grid_details['gridFilters'] = $grid_filters;
                $filter['formData'] = serialize($grid_details);
                $this->db->set("formData", $filter["formData"]);
                $this->db->where("id", $filter["id"]);
                $this->db->update("grid_saved_filters");
            }

            unset($filter);
            unset($grid_filter);
            unset($single_filters);
            unset($single_filter);
        }
    }
}
