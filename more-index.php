<?php
if( $_SERVER['REQUEST_METHOD'] == "POST" ) {
  define('CHARSET','UTF-8');
  mb_internal_encoding('UTF-8');
  mb_regex_encoding('UTF-8');
  function getElementsByClass(&$parentNode, $tagName, $className) {
    $nodes=array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        if (stripos($temp->getAttribute('class'), $className) !== false) {
            $nodes[]=$temp;
        }
    }

    return $nodes;
  }

  $DATE = $_POST['date'];

  $URL = 'https://panchang.astrosage.com/panchang/aajkapanchang?date='.date('j-n-Y',strtotime($DATE)).'&language=hi&lid=1273294';

  $content = file_get_contents($URL);
  libxml_use_internal_errors(true);
  $doc = new DomDocument();
  $doc->validateOnParse = false;
  $doc->loadHTML($content); // That's the addition
  $doc->preserveWhiteSpace = false;
  // $TEST = $doc->getElementByClass('card-shadow bg-white br-radius ui-padding-all ui-margin-all');
  $TEST = getElementsByClass($doc,'div','card-shadow bg-white br-radius ui-padding-all ui-margin-all');

  // $dom = new DomDocument();
  // $dom->load($content);
  // $finder = new DomXPath($dom);
  // $classname="card-shadow bg-white br-radius ui-padding-all ui-margin-all";
  // $TEST = $finder->query("//*[contains(@class, '$classname')]");

  // echo "<pre>";
  $C = [];
  for( $i = 0; $i<=6; $i++){
    // echo trim($TEST[$i]->nodeValue);
    $C[] = preg_replace('/\s+/', ' ',trim($TEST[$i]->nodeValue));
    // $C[] = getElementsByClass($TEST[$i]->nodeValue,'div','col-xs-5 col-sm-3');
  }
  $TEXT = [];
  echo "<pre>";

  $MASTER = [
    ['blank'=>'आज का पंचांग तिथि','tithi'=>'तक नक्षत्र','nak'=>'तक करण','karan'=>'तक पक्ष','paksh'=>'योग','yog'=>'तक वार','vaar'=>'last'],
    ['blank'=>'सूर्य व चन्द्र से संबंधित गणनाएँ सूर्योदय','sunrise'=>'सूर्यास्त','sunset'=>'चन्द्र राशि','moon_sign'=>'चन्द्रोदय','moonrise'=>'चन्द्रास्त','moonset'=>'ऋतु','ritu'=>'last'],
    ['blank'=>'हिन्दू मास एवं वर्ष शक सम्वत','sak_samvat'=>'विक्रम सम्वत','vikram_samvat'=>'काली सम्वत','kali_samvat'=>'प्रविष्टे / गत्ते','pravishta_gatte'=>'मास पूर्णिमांत','purnimanta'=>'मास अमांत','amanta'=>'दिन काल','day_duration'=>'last'],
    ['blank'=>'अशुभ समय (अशुभ मुहूर्त) दुष्टमुहूर्त','dust_muhurat'=>'तक कुलिक','kulik'=>'तक कंटक','kantak'=>'तक राहु काल','rahukaal'=>'तक कालवेला / अर्द्धयाम','kaal_vela'=>'तक यमघण्ट','yamghant'=>'तक यमगण्ड','yamgand'=>'तक गुलिक काल','gulik'=>'last'],
    ['blank'=>'शुभ समय (शुभ मुहूर्त) अभिजीत','abhijit'=>'तक','null'=>'last'],
    ['blank'=>'दिशा शूल दिशा शूल','disha_shool'=>'last'],
    ['blank'=>'चन्द्रबल और ताराबल ताराबल','tara_bal'=>'चन्द्रबल','chandra_bal'=>'last']
  ];

  foreach($C as $k => $v) {
    $p = $v;
    foreach ($MASTER[$k] as $k2 => $v2) {
      if( $k2 == 'blank' ) {
        $p = preg_replace('/\s+/', ' ',trim(str_replace($v2,'',$p)));
      } elseif($v2 == 'last'){
        $TEXT[$k2] = preg_replace('/\s+/', ' ',trim(substr($p,0)));
      } else {
        $TEXT[$k2] = preg_replace('/\s+/', ' ',trim(substr($p,0,strpos($p,$v2))));
        $p = preg_replace('/\s+/', ' ',trim(substr($p,( strpos($p,$v2) + strlen($v2) ) )));
      }
    }
    if( $k==(sizeof($MASTER)-1) ) break;
  }

  /* PART 1 */
  // $p1 = $C[0];
  // $p1 = str_replace('आज का पंचांग तिथि ','',$p1);
  // $TEXT['tithi'] = trim(substr($p1,0,strpos($p1,'तक नक्षत्र')));
  // $p1 = trim(substr($p1,( strpos($p1,'नक्षत्र') + strlen('नक्षत्र') ) ));
  // $TEXT['nak'] = trim(substr($p1,0,strpos($p1,'तक करण')));
  // $p1 = trim(substr($p1,( strpos($p1,'तक करण') + strlen('तक करण') ) ));
  // $TEXT['karan'] = trim(substr($p1,0,strpos($p1,'तक पक्ष')));
  // $p1 = trim(substr($p1,( strpos($p1,'तक पक्ष') + strlen('तक पक्ष') ) ));
  // $TEXT['paksh'] = trim(substr($p1,0,strpos($p1,'योग ')));
  // $p1 = trim(substr($p1,( strpos($p1,'योग ') + strlen('योग') ) ));
  // $TEXT['yog'] = trim(substr($p1,0,strpos($p1,'तक वार')));
  // $p1 = trim(substr($p1,( strpos($p1,'तक वार') + strlen('तक वार') ) ));
  // $TEXT['day'] = trim(substr($p1,0));

  // /* PART 2 */
  // $p2 = $C[1];
  // $p2 = str_replace('सूर्य व चन्द्र से संबंधित गणनाएँ सूर्योदय ','',$p2);
  // $TEXT['sunrise'] = trim(substr($p2,0,strpos($p2,'सूर्यास्त ')));
  // $p2 = trim(substr($p2,( strpos($p2,'सूर्यास्त') + strlen('सूर्यास्त') ) ));
  // $TEXT['sunset'] = trim(substr($p2,0,strpos($p2,'चन्द्र राशि ')));
  // $p2 = trim(substr($p2,( strpos($p2,'चन्द्र राशि') + strlen('चन्द्र राशि') ) ));
  // $TEXT['moon_sign'] = trim(substr($p2,0,strpos($p2,' ')));
  // $p2 = trim(substr($p2,( strpos($p2,'चन्द्रोदय') + strlen('चन्द्रोदय') ) ));
  // $TEXT['moonrise'] = trim(substr($p2,0,strpos($p2,'चन्द्रास्त')));
  // $p2 = trim(substr($p2,( strpos($p2,'चन्द्रास्त') + strlen('चन्द्रास्त') ) ));
  // $TEXT['moonset'] = trim(substr($p2,0,strpos($p2,'ऋतु')));
  // $p2 = trim(substr($p2,( strpos($p2,'ऋतु') + strlen('ऋतु') ) ));
  // $TEXT['ritu'] = trim(substr($p2,0));

  /* PART 3 */
  // $p3 = $C[2];
  // $p3 = trim(str_replace('हिन्दू मास एवं वर्ष शक सम्वत','',$p3));
  // $TEXT['sak_samvat'] = trim(substr($p3,0,strpos($p3,'विक्रम सम्वत')));
  // $p3 = trim(substr($p3,( strpos($p3,'विक्रम सम्वत') + strlen('विक्रम सम्वत') ) ));
  // $TEXT['vikram_samvat'] = trim(substr($p3,0,strpos($p3,'काली सम्वत')));
  // $p3 = trim(substr($p3,( strpos($p3,'काली सम्वत') + strlen('काली सम्वत') ) ));
  // $TEXT['kali_samvat'] = trim(substr($p3,0,strpos($p3,'प्रविष्टे / गत्ते')));
  // $p3 = trim(substr($p3,( strpos($p3,'प्रविष्टे / गत्ते') + strlen('प्रविष्टे / गत्ते') ) ));
  // $TEXT['pravishta_gatte'] = trim(substr($p3,0,strpos($p3,'मास पूर्णिमांत')));
  // $p3 = trim(substr($p3,( strpos($p3,'मास पूर्णिमांत') + strlen('मास पूर्णिमांत') ) ));
  // $TEXT['purnimanta'] = trim(substr($p3,0,strpos($p3,'मास अमांत')));
  // $p3 = trim(substr($p3,( strpos($p3,'मास अमांत') + strlen('मास अमांत') ) ));
  // $TEXT['amanta'] = trim(substr($p3,0,strpos($p3,'दिन काल')));
  // $p3 = trim(substr($p3,( strpos($p3,'दिन काल') + strlen('दिन काल') ) ));
  // $TEXT['day_duration'] = trim(substr($p3,0));
  // echo $p3;
  
  /* PART 4 */
  // $p4 = $C[3];
  // $p4 = trim(str_replace('अशुभ समय (अशुभ मुहूर्त) दुष्टमुहूर्त ','',$p4));
  // $TEXT['dusht_muhurat'] = trim(substr($p4,0,strpos($p4,'तक कुलिक')));
  // $p4 = trim(substr($p4,( strpos($p4,'तक कुलिक') + strlen('तक कुलिक') ) ));
  // $TEXT['kulik'] = trim(substr($p4,0,strpos($p4,'तक कंटक')));
  // $p4 = trim(substr($p4,( strpos($p4,'तक कंटक') + strlen('तक कंटक') ) ));
  // $TEXT['kantak'] = trim(substr($p4,0,strpos($p4,'तक राहु काल')));
  // $p4 = trim(substr($p4,( strpos($p4,'राहु काल') + strlen('राहु काल') ) ));
  // $TEXT['rahu_kaal'] = trim(substr($p4,0,strpos($p4,'तक कालवेला / अर्द्धयाम')));
  // $p4 = trim(substr($p4,( strpos($p4,'तक कालवेला / अर्द्धयाम') + strlen('तक कालवेला / अर्द्धयाम') ) ));
  // $TEXT['kaal_vela'] = trim(substr($p4,0,strpos($p4,'तक यमघण्ट')));
  // $p4 = trim(substr($p4,( strpos($p4,'तक यमघण्ट') + strlen('तक यमघण्ट') ) ));
  // $TEXT['yamghant'] = trim(substr($p4,0,strpos($p4,'तक यमगण्ड')));
  // $p4 = trim(substr($p4,( strpos($p4,'तक यमगण्ड') + strlen('तक यमगण्ड') ) ));
  // $TEXT['yamgand'] = trim(substr($p4,0,strpos($p4,'तक गुलिक काल')));
  // $p4 = trim(substr($p4,( strpos($p4,'तक गुलिक काल') + strlen('तक गुलिक काल') ) ));
  // $TEXT['gulik'] = trim(substr($p4,0));
  // echo $p4;

  /* PART 5 */
  // $p5 = $C[4];
  // $p5 = trim(str_replace('शुभ समय (शुभ मुहूर्त) अभिजीत','',$p5));
  // $TEXT['abhijit'] = trim(substr($p5,0,strpos($p5,'तक')));
  // echo $p5;

  /* PART 6 */
  // $p6 = $C[5];
  // $p6 = trim(str_replace('शुभ समय (शुभ मुहूर्त) अभिजीत','',$p6));
  // $TEXT['abhijit'] = trim(substr($p6,0,strpos($p6,'तक')));
  // echo $p6;

  /* PART 7 */
  $p7 = $C[6];
  // $p7 = trim(str_replace('शुभ समय (शुभ मुहूर्त) अभिजीत','',$p7));
  // $TEXT['abhijit'] = trim(substr($p7,0,strpos($p7,'तक')));
  echo $p7;

  echo '<br><br>';
  echo json_encode($TEXT,JSON_PRETTY_PRINT);
  // print_r($TEXT);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bootstrap Site</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
</head>
<body>
  <div>
    <div class="container mt-5">
      <div class="row">
        <div class="col-lg-6">
          <form action="" method="post">
            <div class="mb-3">
              <input type="date" name="date" class="form-control">
            </div>
            <div class="mb-3">
              <button class="btn btn-primary" type="submit">SUBMIT</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>