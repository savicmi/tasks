<?php

class Kalendar {

    private static $_instance = null;

    private $navigacija = null;
    private $dani = array("Pon","Uto","Sre","Čet","Pet","Sub","Ned");
    private $meseci = array("Januar","Februar","Mart","April","Maj","Jun","Jul","Avgust","Septembar","Oktobar","Novembar","Decembar");
    private $trenutnaGodina = 0;
    private $trenutniMesec = 0;
    private $danasnjiiDatum = null;
    private $brojDana = 0;

    public static function getInstance()
    {

        if (self::$_instance == null)
        {
            self::$_instance = new Kalendar();
        }

        return self::$_instance;
    }

    /**
     * protected constructor to prevent creating a new instance of the
     * Kalendar via the "new" operator from outside of this class
     */
    protected function __construct()
    {
        // returns the filename of the currently executing script
        $this->navigacija = htmlentities($_SERVER['PHP_SELF']);
    }

    /**
     * private clone method to prevent cloning of the instance of the
     * Kalendar instance via the clone operator
     * @return void
     */
    private function __clone() { }

    /**
     * stopping unserialize of an object
     * @return void
     */
    private function __wakeup() { }

    // Magic set and get methods
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : NULL;
    }

    /**
     * print out the calendar
     * @return string $sadrzaj
     */
    public function prikazKalendara() {

        $godina = null;
        $mesec = null;

        // if not already set, $godina and $mesec are set to the current date;
        // gets a two digit numeric representation of a month and a four digit representation of a year
        if (isset($_POST['mesec']) && !empty($_POST['mesec']))
            $mesec = $_POST['mesec'];

        if (isset($_POST['godina']) && !empty($_POST['godina']))
            $godina = $_POST['godina'];

        if (empty($mesec))
            $mesec = isset($_GET['mesec']) ?  $_GET['mesec'] : date("m");
        if (empty($godina))
            $godina = isset($_GET['godina']) ? $_GET['godina'] : date("Y");

        // the valid range of a timestamp is typically from 13 Dec 1901 to 19 Jan 2038,
        // (these are the dates that correspond to the minimum and maximum values for a 32-bit signed integer)
        if ($godina < 1902) {
            $mesec = 1;
            $godina = 1902;
        }
        if ($godina > 2037) {
            $mesec = 12;
            $godina = 2037;
        }

        // current month, year and a number of days in the current month
        $this->trenutniMesec = $mesec;
        $this->trenutnaGodina = $godina;
        $this->danasnjiiDatum = date('Y-m-d');
        $this->brojDana = $this->_brojDana($mesec, $godina);

        $sadrzaj = '<div id="calendar">
                        <div class="calendar_nav">' . $this->_navigacija() . '</div>
                        <div class="calendar_content">
                            <ul class="calendar_label">' . $this->_naziviDana() . '</ul><div class="clearfix"></div>';


        $brojNedelja = $this->_brojNedelja($mesec, $godina);

        // generate weeks in a month
        for ($i = 0; $i < $brojNedelja; $i++) {

            $sadrzaj .= '<ul class="calendar_dates">';

            foreach ($this->_prikazNedelje($i) as $day)
                $sadrzaj .= $day;

            $sadrzaj .= '</ul><div class="clearfix"></div>';
        }

        // close all tags
        $sadrzaj .= '<div class="clearfix"></div>';
        $sadrzaj .= '</div></div>';

        return $sadrzaj;
    }

    /**
     * generate display of a week
     * @return array $daniUNedelji
     */
    private function _prikazNedelje($nedelja) {

        $daniUNnedelji = array();
        static $dan = 1;

        // calculate previous month
        $mesec = $this->trenutniMesec == 1 ? 12 : intval($this->trenutniMesec)-1;
        $godina = $this->trenutniMesec == 1 ? intval($this->trenutnaGodina)-1 : $this->trenutnaGodina;

        // calculate previous and next month
        $sledeciMesec = $this->trenutniMesec == 12 ? 1 : intval($this->trenutniMesec)+1;
        $sledecaGodina = $this->trenutniMesec == 12 ? intval($this->trenutnaGodina)+1 : $this->trenutnaGodina;
        $prethodniMesec = $this->trenutniMesec == 1 ? 12 : intval($this->trenutniMesec)-1;
        $prethodnaGodina = $this->trenutniMesec == 1 ? intval($this->trenutnaGodina)-1 : $this->trenutnaGodina;

        // number of days in the previous month
        $prethodni_brojDana = intval($this->_brojDana($mesec, $godina));

        $prviDan = intval(date('N', strtotime($this->trenutnaGodina . '-' . $this->trenutniMesec . '-' . $dan)));

        for ($i = $prviDan-1; $i > 0; $i--) {

            // check is it today
            $trenutniDatum = strtotime($prethodnaGodina . '-' . $prethodniMesec . '-' . ($prethodni_brojDana-$i+1));

            if (strtotime($this->danasnjiiDatum) - $trenutniDatum == 0)
                $day_li = '<li id="' .($prethodni_brojDana-$i+1). '_' .$prethodniMesec. '" class="today">' .($prethodni_brojDana-$i+1). '</li>';
            else
                $day_li = '<li id="' .($prethodni_brojDana-$i+1). '_' .$mesec. '" class="prev_next">' . ($prethodni_brojDana-$i+1) . '</li>';
            array_push($daniUNnedelji, $day_li);
        }

        $daniSledeci = 0;

        for ($i = $prviDan-1; $i < 7; $i++) {

            if ($dan <= $this->brojDana) {
                // check is it today
                $trenutniDatum = strtotime($this->trenutnaGodina . '-' . $this->trenutniMesec . '-' . $dan);

                if (strtotime($this->danasnjiiDatum) - $trenutniDatum == 0)
                    $day_li = '<li id="' . $dan . '_' . $this->trenutniMesec . '" class="today">' . $dan . '</li>';
                else
                    $day_li = '<li id="' . $dan . '_' . $this->trenutniMesec . '">' . $dan . '</li>';
            }
            else {
                // filling in the last week with the first days of next month
                $daniSledeci++;

                // check is it today
                $trenutniDatum = strtotime($sledecaGodina . '-' . $sledeciMesec . '-' . $daniSledeci);

                if (strtotime($this->danasnjiiDatum) - $trenutniDatum == 0)
                    $day_li = '<li id="' .$daniSledeci. '_' .$sledeciMesec. '" class="today">' .$daniSledeci. '</li>';
                else
                    $day_li = '<li id="' .$daniSledeci. '_' .$sledeciMesec. '" class="prev_next">' .$daniSledeci. '</li>';
            }
            array_push($daniUNnedelji, $day_li);

            $dan++;
        }

        return $daniUNnedelji;


    }

    /**
     * create the navigation for previous and next month
     * @return string $navigacija
     */
    private function _navigacija() {

        $sledeciMesec = $this->trenutniMesec == 12 ? 1 : intval($this->trenutniMesec)+1;
        $sledecaGodina = $this->trenutniMesec == 12 ? intval($this->trenutnaGodina)+1 : $this->trenutnaGodina;
        $prethodniMesec = $this->trenutniMesec == 1 ? 12 : intval($this->trenutniMesec)-1;
        $prethodnaGodina = $this->trenutniMesec == 1 ? intval($this->trenutnaGodina)-1 : $this->trenutnaGodina;

        $navigacija = '<div class="calendar_header">
                            <a id="calendar_prev" href="' . 'index.php' . '?mesec=' . sprintf('%02d', $prethodniMesec) . '&amp;godina=' . $prethodnaGodina.'">PRETHODNI</a>
                            <span class="calendar_title">' . $this->_nazivMeseca(date('n', strtotime($this->trenutnaGodina . '-' . $this->trenutniMesec . '-1'))) . ' ' . date('Y', strtotime($this->trenutnaGodina . '-' . $this->trenutniMesec . '-1')) . '</span>
                            <a id="calendar_next" href="' . 'index.php' . '?mesec=' . sprintf('%02d', $sledeciMesec) . '&amp;godina=' . $sledecaGodina.'">SLEDEĆI</a>
                            <div class="clearfix"></div>
                       </div>';

        return $navigacija;
    }

    /**
     * create labels for days in a week
     * @return string $sadrzaj
     */
    private function _naziviDana() {
        $sadrzaj = '';
        foreach ($this->dani as $index => $label)
            $sadrzaj .= '<li>' . $label.'</li>';

        return $sadrzaj;
    }

    /**
     * create a label for given month
     * @return string (name of the month)
     */
    private function _nazivMeseca($mes) {
        return $this->meseci[$mes-1];
    }

    /**
     * calculate number of days in a particular month
     * @return string (the formatted date string)
     */
    private function _brojDana($mes = null, $god = null) {

        ($mes == null) ? $mesec = date("m") : $mesec = $mes;
        ($god == null) ? $godina = date("Y") : $godina = $god;

        return date('t', strtotime($godina . '-' . $mesec . '-01'));
    }

    /**
     * calculate number of weeks in a particular month
     * @return int $brojNedelja
     */
    private function _brojNedelja($mes = null, $god = null) {

        ($mes == null) ? $mesec = date("m") : $mesec = $mes;
        ($god == null) ? $godina = date("Y") : $godina = $god;

        // find number of days in this month
        $brojDana = $this->_brojDana($mesec, $godina);

        // calculate number of weeks
        $brojNedelja = ($brojDana % 7 == 0 ? 0 : 1) + intval($brojDana / 7);

        // gets numeric representation of a day (1 for Monday, 7 for Sunday) for the first and last day in the month
        $prviDan = date('N', strtotime($godina . '-' . $mesec . '-01'));
        $poslednjiDan = date('N', strtotime($godina . '-' . $mesec . '-' . $brojDana));

        if ($poslednjiDan < $prviDan)
            $brojNedelja++;

        return $brojNedelja;
    }
}

// communication with jQuery Ajax methods
if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];

    switch($action) {
        case 'get_month' : get_month();break;
    }
}

function get_month()
{
    $new_kalendar = Kalendar::getInstance();
    $month = $new_kalendar->prikazKalendara();

    $json = array('calendar'=>$month);
    echo json_encode($json);
}