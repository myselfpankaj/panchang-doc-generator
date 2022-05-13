<?php
define('CHARSET', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

$DATE = date('Y-m-d');
$MONTHS = [
  1 => 'जनवरी',
  2 => 'फरवरी',
  3 => 'मार्च',
  4 => 'अप्रैल',
  5 => 'मई',
  6 => 'जून',
  7 => 'जुलाई',
  8 => 'अगस्त',
  9 => 'सितंबर',
  10 => 'अक्टूबर',
  11 => 'नवंबर',
  12 => 'दिसंबर'
];
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  function getElementsByClass(&$parentNode, $tagName, $className)
  {
    $nodes = array();
    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
      $temp = $childNodeList->item($i);
      if (stripos($temp->getAttribute('class'), $className) !== false) {
        $nodes[] = $temp;
      }
    }
    return $nodes;
  }

  $DATE = $_POST['date'];

  $URL = 'https://panchang.astrosage.com/panchang/aajkapanchang?date=' . date('j-n-Y', strtotime($DATE)) . '&language=hi&lid=1273294';

  $content = file_get_contents($URL);
  libxml_use_internal_errors(true);
  $doc = new DomDocument();
  $doc->validateOnParse = false;
  $doc->loadHTML($content); // That's the addition
  $doc->preserveWhiteSpace = false;
  $TEST = getElementsByClass($doc, 'div', 'card-shadow bg-white br-radius ui-padding-all ui-margin-all');
  $C = [];
  for ($i = 0; $i <= 6; $i++) {
    $C[] = preg_replace('/\s+/', ' ', trim($TEST[$i]->nodeValue));
  }
  $TEXT = [];

  $MASTER = [
    ['blank' => 'आज का पंचांग तिथि', 'tithi' => 'तक नक्षत्र', 'nak' => 'तक करण', 'karan' => 'तक पक्ष', 'paksh' => 'योग', 'yog' => 'तक वार', 'vaar' => 'last'],
    ['blank' => 'सूर्य व चन्द्र से संबंधित गणनाएँ सूर्योदय', 'sunrise' => 'सूर्यास्त', 'sunset' => 'चन्द्र राशि', 'moon_sign' => 'चन्द्रोदय', 'moonrise' => 'चन्द्रास्त', 'moonset' => 'ऋतु', 'ritu' => 'last'],
    ['blank' => 'हिन्दू मास एवं वर्ष शक सम्वत', 'sak_samvat' => 'विक्रम सम्वत', 'vikram_samvat' => 'काली सम्वत', 'kali_samvat' => 'प्रविष्टे / गत्ते', 'pravishta_gatte' => 'मास पूर्णिमांत', 'purnimanta' => 'मास अमांत', 'amanta' => 'दिन काल', 'day_duration' => 'last'],
    ['blank' => 'अशुभ समय (अशुभ मुहूर्त) दुष्टमुहूर्त', 'dust_muhurat' => 'तक कुलिक', 'kulik' => 'तक कंटक', 'kantak' => 'तक राहु काल', 'rahukaal' => 'तक कालवेला / अर्द्धयाम', 'kaal_vela' => 'तक यमघण्ट', 'yamghant' => 'तक यमगण्ड', 'yamgand' => 'तक गुलिक काल', 'gulik' => 'तक','null'=>'last'],
    ['blank' => 'शुभ समय (शुभ मुहूर्त) अभिजीत', 'abhijit' => 'तक', 'null' => 'last'],
    ['blank' => 'दिशा शूल दिशा शूल', 'disha_shool' => 'last'],
    ['blank' => 'चन्द्रबल और ताराबल ताराबल', 'tara_bal' => 'चन्द्रबल', 'chandra_bal' => 'last']
  ];

  foreach ($C as $k => $v) {
    $p = $v;
    foreach ($MASTER[$k] as $k2 => $v2) {
      if ($k2 == 'blank') $p = preg_replace('/\s+/', ' ', trim(str_replace($v2, '', $p)));
      elseif ($v2 == 'last') $TEXT[$k2] = preg_replace('/\s+/', ' ', trim(substr($p, 0)));
      else {
        $TEXT[$k2] = preg_replace('/\s+/', ' ', trim(substr($p, 0, strpos($p, $v2))));
        $p = preg_replace('/\s+/', ' ', trim(substr($p, (strpos($p, $v2) + strlen($v2)))));
      }
    }
    if ($k == (sizeof($MASTER) - 1)) break;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Astrosage Panchang Data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      color: black;
    }
    .ps-top{
      position: sticky;
      top: 50px;
    }
  </style>
</head>

<body>
  <div>
    <div class="container mt-5">
      <div class="row">
        <div class="col-lg-6">
          <div class="card ps-top">
            <div class="card-header fw-bold bg-primary text-white">SELECT DATE</div>
            <div class="card-body">
              <form action="" method="post">
                <div class="mb-3">
                  <input type="date" name="date" value="<?php echo $DATE; ?>" class="form-control">
                </div>
                <div class="mb-3">
                  <button class="btn btn-primary" type="submit">SUBMIT</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card ps-top">
            <div class="card-header fw-bold bg-success text-white">OUTPUT</div>
            <div class="card-body">
              <?php if (isset($TEXT['tithi'])) : ?>
                <h6 class="fw-bold"><?php echo $URL; ?></h6>
                <br>
                <h5 class="text-danger fw-bold">(<?php echo date('d', strtotime($DATE)) . ' ' . $MONTHS[date('n', strtotime($DATE))] . ' ' . date('Y', strtotime($DATE)) ?> का पंचांग)</h5>
                <br>
                <h6 class="fw-bold">(<?php echo date('l', strtotime($DATE)) ?>)</h6>
                <br>
                <h6 class="fw-bold">आज का पंचांग</h6>
                <h6><b>आज की तिथि:</b> <?php echo $TEXT['tithi']; ?> तक</h6>
                <h6><b>आज का नक्षत्र:</b> <?php echo $TEXT['nak']; ?> तक</h6>
                <h6><b>आज का करण:</b> <?php echo $TEXT['karan']; ?> तक</h6>
                <h6><b>आज का पक्ष:</b> <?php echo $TEXT['paksh']; ?></h6>
                <h6><b>आज का योग:</b> <?php echo $TEXT['yog']; ?> तक</h6>
                <h6><b>आज का वार:</b> <?php echo $TEXT['vaar']; ?></h6>
                <br>

                <h5 class="fw-bold">आज का सूर्योदय-सूर्यास्त और चंद्रोदय-चंद्रास्त का समय</h5>
                <h6><b>आज का सूर्योदय:</b> <?php echo $TEXT['sunrise']; ?> पर</h6>
                <h6><b>आज का सूर्यास्त:</b> <?php echo $TEXT['sunset']; ?> पर</h6>
                <h6><b>आज का चन्द्रोदय:</b> <?php echo $TEXT['moonrise']; ?> पर</h6>
                <h6><b>आज का चन्द्रास्त:</b> <?php echo $TEXT['moonset']; ?> पर</h6>
                <h6><b>आज की चन्द्र राशि:</b> <?php echo $TEXT['moon_sign']; ?></h6>
                <h6><b>आज की ऋतु:</b> <?php echo $TEXT['ritu']; ?></h6>
                <br>

                <h5 class="fw-bold">आज का हिन्दू मास एवं वर्ष</h5>
                <h6><b>शक सम्वत:</b> <?php echo trim($TEXT['sak_samvat']); ?>, <b>विक्रम सम्वत:</b> <?php echo $TEXT['vikram_samvat']; ?>, <b>काली सम्वत:</b> <?php echo $TEXT['kali_samvat']; ?></h6>
                <h6><b>प्रविष्टा गत्ते:</b> <?php echo $TEXT['pravishta_gatte']; ?></h6>
                <h6><b>आज का दिन काल:</b> <?php
                                          $DD = explode(':', $TEXT['day_duration']);
                                          echo $DD[0] . ' घंटे: ' . $DD[1] . ' मिनट: ' . $DD[2] . ' सेकंड';
                                          ?></h6>
                <h6><b>मास अमांत:</b> <?php echo trim($TEXT['amanta']); ?>, <b>मास पूर्णिमांत:</b> <?php echo $TEXT['purnimanta']; ?></h6>
                <br>

                <h5 class="fw-bold">आज का शुभ, अशुभ समय एवं मुहूरत</h5>
                <h6><b>आज का शुभ समय:</b> अभिजीत - <?php echo $TEXT['abhijit']; ?> तक</h6>
                <h6><b>अशुभ समय (अशुभ मुहूरत, दुष्ट मुहूरत):</b> <?php echo  preg_replace('/\s+/', ' ', trim(substr($TEXT['dust_muhurat'], 0, strpos($TEXT['dust_muhurat'], 'तक,')))); ?> तक</h6>
                <h6><b>कुलिक:</b> <?php echo $TEXT['kulik']; ?> तक, <b>कंटक:</b> <?php echo $TEXT['kantak']; ?> तक</h6>
                <br>

                <h5 class="fw-bold">आज का राहु काल:</h5>
                <h6><?php
                    if (in_array(date('N', strtotime($DATE)), [1, 5, 6])) echo 'सुबह';
                    elseif (in_array(date('N', strtotime($DATE)), [2, 3, 4])) echo 'दोपहर';
                    else echo 'शाम';
                    ?> <?php echo $TEXT['rahukaal']; ?> तक</h6>
                <h6><b>कालवेला अर्द्धयाम:</b> <?php echo $TEXT['kaal_vela']; ?> तक</h6>
                <h6><b>यमघण्ट:</b> <?php echo $TEXT['yamghant']; ?> तक</h6>
                <h6><b>यमगण्ड:</b> <?php echo $TEXT['yamgand']; ?> तक</h6>
                <h6><b>गुलिक काल:</b> <?php echo $TEXT['gulik']; ?> तक</h6>
                <br>

                <h5><b>आज का दिशाशूल -</b> <?php echo $TEXT['disha_shool']; ?> दिशा में रहेगा</h5>
                <br>

                <h5 class="fw-bold">आज का चन्द्रबल और ताराबल</h5>
                <h6><b>आज का ताराबल, - </b> <?php echo $TEXT['tara_bal']; ?></h6><br>
                <h6><b>आज का चन्द्रबल, - </b> <?php echo $TEXT['chandra_bal']; ?></h6>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>