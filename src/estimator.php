<?php


function getTime($periodType, $timeToElapse)
{
    //pow(2,10)=30 days
    switch ($periodType) {
        case 'days':
            $val = floor(($timeToElapse) / 3);
            return pow(2, $val); //8744960 > 1561600
        case 'weeks':
            $val = floor(($timeToElapse * 7) / 3);
            return pow(2, $val);//25948160 > 2429340876800
        case 'months':
//            $weeks=$timeToElapse*4;
//            $val=floor(($weeks * 7)/3);
            $val = floor(($timeToElapse * 30) / 3);
            return pow(2, $val); //39444480> 20195573760
        default:
            return 1;
    }
}

function checkIfArray($data)
{
    if (is_array($data)) {
        return $data;
    } else {
        return json_decode($data);
    }
}

function doOutput()
{

}

/*Sample input data
*{
  region: {
      name: "Africa",
      avgAge: 19.7,
      avgDailyIncomeInUSD: 5,
      avgDailyIncomePopulation: 0.71
    },
  periodType: "days",
  timeToElapse: 58,
  reportedCases: 674,
  population: 66622705,
  totalHospitalBeds: 1380614
}
*/

function covid19ImpactEstimator($data)
{

    $parsedData = checkIfArray($data);

//    echo(json_encode($parsedData));

    $name = ($parsedData['region']['name']);
    $avgAge = doubleval($parsedData['region']['avgAge']);
    $avgDailyIncomeInUSD = doubleval($parsedData['region']['avgDailyIncomeInUSD']);
    $avgDailyIncomePopulation = doubleval($parsedData['region']['avgDailyIncomePopulation']);
    $periodType = ($parsedData['periodType']);
    $timeToElapse = ($parsedData['timeToElapse']);
    $reportedCases = intval($parsedData['reportedCases']);
    $population = intval($parsedData['population']);
    $totalHospitalBeds = intval($parsedData['totalHospitalBeds']);

    $time = getTime($periodType, $timeToElapse);

//    die($time);

    $occupiedBeds = 0.65 * $totalHospitalBeds;
    $bedsAvailable = $totalHospitalBeds * 0.35;

    $impact = [];
    $impact['currentlyInfected'] = $reportedCases * 10;
    $impact['infectionsByRequestedTime'] = floor(intval($impact['currentlyInfected']) * $time);
    $impact['severeCasesByRequestedTime'] = floor(intval($impact['infectionsByRequestedTime']) * (15 / 100));//require hospitalization
    $impact['hospitalBedsByRequestedTime'] = floor($bedsAvailable-intval($impact['severeCasesByRequestedTime']));
    $impact['casesForICUByRequestedTime'] = floor(intval($impact['infectionsByRequestedTime']) * (5 / 100));//require icucare
    $impact['casesForVentilatorsByRequestedTime'] = floor(intval($impact['infectionsByRequestedTime']) * (2 / 100));//require ventilators
    $impact['dollarsInFlight'] = ((intval($impact['infectionsByRequestedTime']) * $avgDailyIncomePopulation) * $avgDailyIncomeInUSD * 30);//economy loss 30 days


    $severeImpact = [];
    $severeImpact['currentlyInfected'] = $reportedCases * 50;
    $severeImpact['infectionsByRequestedTime'] = floor(intval($severeImpact['currentlyInfected']) * $time);
    $severeImpact['severeCasesByRequestedTime'] = floor(intval($severeImpact['infectionsByRequestedTime']) * (15 / 100));//require hospitalization
    $severeImpact['hospitalBedsByRequestedTime'] = floor($bedsAvailable-intval($severeImpact['severeCasesByRequestedTime']));
    $severeImpact['casesForICUByRequestedTime'] = floor(intval($severeImpact['infectionsByRequestedTime']) * (5 / 100));//require icucare
    $severeImpact['casesForVentilatorsByRequestedTime'] = floor(intval($severeImpact['infectionsByRequestedTime']) * (2 / 100));//require ventilators
    $severeImpact['dollarsInFlight'] = ((intval($severeImpact['infectionsByRequestedTime']) * $avgDailyIncomePopulation) * $avgDailyIncomeInUSD * 30);//economy loss 30 days


    $response = [];
    $response['data'] = $parsedData;
    $response['impact'] = $impact;
    $response['severeImpact'] = $severeImpact;
    return $response;
}


//{
//    region: {
//    name: "Africa",
//      avgAge: 19.7,
//      avgDailyIncomeInUSD: 5,
//      avgDailyIncomePopulation: 0.71
//    },
//    periodType: "days",
//  timeToElapse: 58,
//  reportedCases: 674,
//  population: 66622705,
//  totalHospitalBeds: 1380614
//}

function array_to_xml($student_info, &$xml_student_info)
{
    foreach ($student_info as $key => $value) {
        if (is_array($value)) {
            $key = is_numeric($key) ? "item$key" : $key;
            $subnode = $xml_student_info->addChild("$key");
            array_to_xml($value, $subnode);
        } else {
            $key = is_numeric($key) ? "item$key" : $key;
            $xml_student_info->addChild("$key", "$value");
        }
    }
}

function test()
{
    $data['region'] = [
        'name' => 'Africa',
        'avgAge' => 19.7,
        'avgDailyIncomeInUSD' => 5,
        'avgDailyIncomePopulation' => 0.71,
    ];

    $data['periodType'] = 'days';
    $data['timeToElapse'] = 58;
    $data['reportedCases'] = 674;
    $data['population'] = 66622705;
    $data['totalHospitalBeds'] = 1380614;
    return $data;
//    die(json_encode($data));
//    die(covid19ImpactEstimator($data));
}