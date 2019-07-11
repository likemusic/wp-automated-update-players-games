<?php

namespace Likemusic\AutomatedUpdatePlayersGames\Helper;

class CountryCodeLatinToCyrillicConverter
{
    /**
     * @var array
     *
     * @see https://ru.wikipedia.org/wiki/ГОСТ_7.67
     */
    private $map = [
        'AFG' => 'АФГ', //	Афганистан
        'AGO' => 'АНГ', //	Ангола
        'AIA' => 'АНА', //	Ангилья
        'ALB' => 'АЛБ', //	Албания
        'AND' => 'АНД', //	Андорра
        'ANT' => 'АНТ', //	Антильские острова (Нидерландские Антилы)
        'ARE' => 'ОБЭ', //	Объединённые Арабские Эмираты
        'ARG' => 'АРГ', //	Аргентина
        'ARM' => 'АРМ', //	Армения
        'ASM' => 'ВОС', //	Восточное Самоа
        'ATA' => 'АНК', //	Антарктика
        'ATF' => 'ФРЮ', //	Французские Южные территории
        'ATG' => 'АНР', //	Антигуа и Барбуда
        'AUS' => 'АВС', //	Австралия
        'AUT' => 'АВТ', //	Австрия
        'AZE' => 'АЗЕ', //	Азербайджан
        'BDI' => 'БУР', //	Бурунди
        'BEL' => 'БЕЛ', //	Бельгия
        'BEN' => 'БЕН', //	Бенин
        'BFA' => 'БУК', //	Буркина-Фасо
        'BGD' => 'БАН', //	Бангладеш
        'BGR' => 'БОГ', //	Болгария
        'BHR' => 'БАХ', //	Бахрейн
        'BHS' => 'БАГ', //	Багамы
        'BIH' => 'БОС', //	Босния и Герцеговина
        'BLR' => 'БЕИ', //	Беларусь
        'BLZ' => 'БЕЗ', //	Белиз
        'BMU' => 'БЕР', //	Бермуды
        'BOL' => 'БОЛ', //	Боливия
        'BRA' => 'БРА', //	Бразилия
        'BRB' => 'БАР', //	Барбадос
        'BRN' => 'БРУ', //	Бруней-Даруссалам
        'BTN' => 'БУТ', //	Бутан
        'BVT' => 'БУВ', //	Буве, остров
        'BWA' => 'БОТ', //	Ботсвана
        'CAF' => 'ЦЕН', //	Центрально-Африканская Республика
        'CAN' => 'КАН', //	Канада
        'CCK' => 'КОК', //	Кокосовые (Килинг) острова
        'CHE' => 'ШВА', //	Швейцария
        'CHL' => 'ЧИЛ', //	Чили
        'CHN' => 'КИТ', //	Китай, см. также Аомынь (Макао), Гонконг
        'CIV' => 'КОТ', //	Кот-д’Ивуар
        'CMR' => 'КАМ', //	Камерун
        'COD' => 'КОО', //	Конго (Киншаса)
        'COG' => 'КОН', //	Конго (Браззавиль)
        'COK' => 'КУК', //	Кука, острова
        'COL' => 'КОЛ', //	Колумбия
        'COM' => 'КОМ', //	Коморы
        'CPV' => 'КАБ', //	Кабо-Верде
        'CRI' => 'КОС', //	Коста-Рика
        'CUB' => 'КУБ', //	Куба
        'CXR' => 'РОЖ', //	Рождества (Кристмас), остров
        'CYM' => 'КАЙ', //	Кайман, острова
        'CYP' => 'КИП', //	Кипр
        'CZE' => 'ЧЕШ', //	Чехия
        'DEU' => 'ГЕР', //	Германия
        'DJI' => 'ДЖИ', //	Джибути
        'DMA' => 'ДОМ', //	Доминика
        'DNK' => 'ДАН', //	Дания
        'DOM' => 'ДОН', //	Доминиканская Республика
        'DZA' => 'АЛЖ', //	Алжир
        'ECU' => 'ЭКА', //	Эквадор
        'EGY' => 'ЕГИ', //	Египет
        'ERI' => 'ЭРИ', //	Эритрея
        'ESH' => 'ЗАП', //	Западная Сахара
        'ESP' => 'ИСП', //	Испания
        'EST' => 'ЭСТ', //	Эстония
        'ETH' => 'ЭФИ', //	Эфиопия
        'FIN' => 'ФИН', //	Финляндия
        'FJI' => 'ФИД', //	Фиджи
        'FLK' => 'ФОЛ', //	Фолклендские (Мальвинские) острова
        'FRA' => 'ФРА', //	Франция
        'FRO' => 'ФАР', //	Фарерские острова
        'FSM' => 'МИК', //	Микронезия (Федеративные Штаты Микронезии)
        'FXX' => 'ФРМ', //	Франция, Метрополия
        'GAB' => 'ГАБ', //	Габон
        'GBR' => 'ВЕЛ', //	Великобритания (Соединённое Королевство)
        'GEO' => 'ГРУ', //	Грузия
        'GHA' => 'ГАН', //	Гана
        'GIB' => 'ГИБ', //	Гибралтар
        'GIN' => 'ГВН', //	Гвинея
        'GLP' => 'ГВА', //	Гваделупа
        'GMB' => 'ГАМ', //	Гамбия
        'GNB' => 'ГВЯ', //	Гвинея-Бисау
        'GNQ' => 'ЭКВ', //	Экваториальная Гвинея
        'GRC' => 'ГРИ', //	Греция
        'GRD' => 'ГРА', //	Гренада
        'GRL' => 'ГРЕ', //	Гренландия
        'GTM' => 'ГВЕ', //	Гватемала
        'GUF' => 'ГВИ', //	Гвиана Французская
        'GUM' => 'ГУА', //	Гуам
        'GUY' => 'ГАЙ', //	Гайана
        'HKG' => 'ГОО', //	Гонконг, специальный административный район Китая
//'HKG' => 'СЯН', //	Сянган (Гонконг)
        'HMD' => 'ХЕМ', //	Херд и Макдональд, острова
        'HND' => 'ГОН', //	Гондурас
        'HRV' => 'ХОР', //	Хорватия
        'HTI' => 'ГАИ', //	Гаити
        'HUN' => 'ВЕН', //	Венгрия
        'IDN' => 'ИНЗ', //	Индонезия
        'IND' => 'ИНД', //	Индия
        'IOT' => 'БРИ', //	Британская территория в Индийском океане
        'IRL' => 'ИРЯ', //	Ирландия
        'IRN' => 'ИРН', //	Иран
        'IRQ' => 'ИРК', //	Ирак
        'ISL' => 'ИСЛ', //	Исландия
        'ISR' => 'ИЗР', //	Израиль
        'ITA' => 'ИТА', //	Италия
        'JAM' => 'ЯМА', //	Ямайка
        'JOR' => 'ИОР', //	Иордания
        'JPN' => 'ЯПО', //	Япония
        'KAZ' => 'КАЗ', //	Казахстан
        'KEN' => 'КЕН', //	Кения
        'KHM' => 'КАК', //	Камбоджа
        'KIR' => 'КИР', //	Кирибати
        'KNA' => 'СЕС', //	Сент-Китс и Невис
        'KOR' => 'КОР', //	Корея, Республика
        'KRZ' => 'КЫР', //	Кыргызстан (Киргизия)
        'KWT' => 'КУВ', //	Кувейт
        'LAO' => 'ЛАО', //	Лаос
        'LBN' => 'ЛИВ', //	Ливан
        'LBR' => 'ЛИБ', //	Либерия
        'LBY' => 'ЛИИ', //	Ливийская Арабская Джамахирия (Ливия)
        'LCA' => 'СЕТ', //	Сент-Люсия
        'LIE' => 'ЛИХ', //	Лихтенштейн
        'LKA' => 'ШРИ', //	Шри-Ланка
        'LSO' => 'ЛЕС', //	Лесото
        'LTU' => 'ЛИТ', //	Литва
        'LUX' => 'ЛЮК', //	Люксембург
        'LVA' => 'ЛАТ', //	Латвия
        'MAC' => 'АОМ', //	Аомынь (Макао), специальный административный район Китая
        'MAR' => 'МАР', //	Марокко
        'MCO' => 'МОН', //	Монако
        'MDA' => 'МОЛ', //	Молдова (Республика Молдова)
        'MDG' => 'МАГ', //	Мадагаскар
        'MDV' => 'МАЛ', //	Мальдивы
        'MEX' => 'МЕК', //	Мексика
        'MHL' => 'МАШ', //	Маршалловы острова
        'MKD' => 'МАД', //	Македония
        'MLI' => 'МАИ', //	Мали
        'MLT' => 'МАМ', //	Мальта
        'MMR' => 'МЬЯ', //	Мьянма
        'MNG' => 'МОО', //	Монголия
        'MNP' => 'СЕВ', //	Северные Марианские острова
        'MOZ' => 'МОЗ', //	Мозамбик
        'MRT' => 'МАВ', //	Мавритания
        'MSR' => 'МОТ', //	Монтсеррат
        'MTQ' => 'МАТ', //	Мартиника
        'MUS' => 'МАБ', //	Маврикий
        'MWI' => 'МАЕ', //	Малави
        'MYS' => 'МАЗ', //	Малайзия
        'MYT' => 'МАО', //	Маоре (Майотта)
        'NAM' => 'НАМ', //	Намибия
        'NCL' => 'НОК', //	Новая Каледония
        'NER' => 'НИА', //	Нигер
        'NFK' => 'НОФ', //	Норфолк, остров
        'NGA' => 'НИГ', //	Нигерия
        'NIC' => 'НИК', //	Никарагуа
        'NIU' => 'НИУ', //	Ниуэ
        'NLD' => 'НИД', //	Нидерланды
        'NOR' => 'НОР', //	Норвегия
        'NPL' => 'НЕП', //	Непал
        'NRU' => 'НАУ', //	Науру
        'NZL' => 'НОЗ', //	Новая Зеландия
        'OMN' => 'ОМА', //	Оман
        'PAK' => 'ПАК', //	Пакистан
        'PAN' => 'ПАН', //	Панама
        'PCN' => 'ПИТ', //	Питкэрн
        'PER' => 'ПЕР', //	Перу
        'PHL' => 'ФИЛ', //	Филиппины
        'PLW' => 'ПАЛ', //	Палау
        'PNG' => 'ПАП', //	Папуа-Новая Гвинея
        'POL' => 'ПОЛ', //	Польша
        'PRI' => 'ПУЭ', //	Пуэрто-Рико
        'PRK' => 'КОП', //	Корейская Народно-Демократическая Республика
        'PRT' => 'ПОР', //	Португалия
        'PRY' => 'ПАР', //	Парагвай
        'PYF' => 'ФРП', //	Французская Полинезия
        'QAT' => 'КАТ', //	Катар
        'REU' => 'РЕЮ', //	Реюньон
        'ROU' => 'РУМ', //	Румыния
        'RUS' => 'РОФ', //	Россия (Российская Федерация)
        'RWA' => 'РУА', //	Руанда
        'SAU' => 'САУ', //	Саудовская Аравия
        'SDN' => 'СУД', //	Судан
        'SEN' => 'СЕН', //	Сенегал
        'SGP' => 'СИН', //	Сингапур
        'SGS' => 'ЮЖГ', //	Южная Георгия и Южные Сандвичевы острова
        'SHN' => 'СВЯ', //	Святая Елена
        'SJM' => 'СВБ', //	Свальбард (Шпицберген) и Ян-Майен
        'SLB' => 'СОЛ', //	Соломоновы Острова
        'SLE' => 'СЬЕ', //	Сьерра-Леоне
        'SLV' => 'САЛ', //	Сальвадор
        'SMR' => 'САН', //	Сан-Марино
        'SOM' => 'СОМ', //	Сомали
        'SPM' => 'СЕП', //	Сен-Пьер и Микелон
        'STP' => 'САТ', //	Сан-Томе и Принсипи
        'SUR' => 'СУР', //	Суринам
        'SVK' => 'СЛА', //	Словакия
        'SVN' => 'СЛО', //	Словения
        'SWE' => 'ШВЕ', //	Швеция
        'SWZ' => 'СВА', //	Свазиленд
        'SYC' => 'СЕЙ', //	Сейшелы
        'SYR' => 'СИР', //	Сирия
        'TCA' => 'ТЁР', //	Теркс и Кайкос
        'TCD' => 'ЧАД', //	Чад
        'TGO' => 'ТОГ', //	Того
        'THA' => 'ТАИ', //	Таиланд
        'TJK' => 'ТАД', //	Таджикистан
        'TKL' => 'ТОК', //	Токелау
        'TKM' => 'ТУР', //	Туркменистан
        'TMP' => 'ВОТ', //	Восточный Тимор
        'TON' => 'ТОН', //	Тонга
        'TTO' => 'ТРИ', //	Тринидад и Тобаго
        'TUN' => 'ТУН', //	Тунис
        'TUR' => 'ТУЦ', //	Турция
        'TUV' => 'ТУВ', //	Тувалу
        'TWN' => 'ТАЙ', //	Тайвань, провинция Китая
        'TZA' => 'ТАН', //	Танзания
        'UGA' => 'УГА', //	Уганда
        'UKR' => 'УКР', //	Украина
        'UMI' => 'МЕЛ', //	Мелкие отдалённые острова США
        'URY' => 'УРУ', //	Уругвай
        'USA' => 'СОЕ', //	Соединённые Штаты Америки (США)
        'UZB' => 'УЗБ', //	Узбекистан
        'VAT' => 'ВАТ', //	Ватикан
        'VCT' => 'СЕР', //	Сент-Винсент и Гренадины
        'VEN' => 'ВЕС', //	Венесуэла
        'VGB' => 'ВИБ', //	Виргинские острова (Британские)
        'VIR' => 'ВИР', //	Виргинские острова (США)
        'VNM' => 'ВЬЕ', //	Вьетнам
        'VUT' => 'ВАН', //	Вануату
        'WLF' => 'УОЛ', //	Уоллис и Футуна
        'WSM' => 'САМ', //	Самоа
        'YEM' => 'ЙЕМ', //	Йемен
        'YUG' => 'ЮГО', //	Югославия
        'ZAF' => 'ЮЖН', //	Южно-Африканская Республика
        'ZMB' => 'ЗАМ', //	Замбия
        'ZWE' => 'ЗИМ', //	Зимбабве
    ];


    public function getCyrillicCountryCode($latinCountryCode)
    {
        if (!array_key_exists($latinCountryCode, $latinCountryCode)) {
            return $latinCountryCode;
        }

        return $this->map[$latinCountryCode];
    }
}
