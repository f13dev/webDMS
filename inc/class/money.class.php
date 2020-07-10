<?php 
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    header("Location: ../../");
  }
Class Money {
    // Variables
    private $id,$name,$unit,$frequency,$amount,$start,$next,$income;

    public function __construct($id,$name,$unit,$frequency,$amount,$start,$income) {
        $this->setId($id);
        $this->setName($name);
        $this->setUnit($unit);
        $this->setFrequency($frequency);
        $this->setAmount($amount);
        $this->setStart($start);
        $this->setIncome($income);
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function getFrequency() {
        return $this->frequency;
    }

    public function getAmount() {
        return sprintf('%0.2f', round($this->amount,2));
        return $this->amount;
    }

    public function getStart() {
        return $this->start;
    }

    public function getIncome() {
        return $this->income;
    }

    public function getNext() {
        return $this->next;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }

    public function setFrequency($frequency) {
        $this->frequency = $frequency;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setStart($start) {
        $this->start = $start;
    }

    public function setIncome($income) {
        $this->income = $income;
    }

    public function setNext() {
        $payment = date_create_from_format('Y-m-d', $this->getStart());
        $today = new DateTime();
        while ($payment < $today) {
            if ($this->getUnit() == '1') {
                $payment->modify('+' . $this->getFrequency() . ' year');
            } else 
            if ($this->getUnit() == '12') {
                $payment->modify('+' . $this->getFrequency() . ' month');
            } else 
            if ($this->getUnit() == '52') {
                $payment->modify('+' . $this->getFrequency() . ' week');
            } else 
            if ($this->getUnit() == '365') {
                $payment->modify('+' . $this->getFrequency() . ' day');
            } else {
                return false;
            }
        }
        return $payment->format('Y,m,d');
    }

    public function getAnnual() {
        return sprintf('%0.2f', round($this->getAmount() / $this->getFrequency() * $this->getUnit(),2));
    }

    public function getMonthly() {
        return sprintf('%0.2f', round($this->getAnnual() / 12,2));
    }

    public function getWeekly() {
        return sprintf('%0.2f', round($this->getAnnual() / 52,2));
    }

    public function getDaily() {
        return sprintf('%0.2f', round($this->getAnnual() / 365,2));
    }
}