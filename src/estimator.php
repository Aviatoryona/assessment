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
            $val = floor(($timeToElapse * 30) / 3);
            return pow(2, $val); //39444480> 20195573760
        default:
            return 1;
    }
}

function getDays($periodType, $timeToElapse)
{
    switch ($periodType) {
        case 'days':
            return $timeToElapse;
        case 'weeks':
            return ($timeToElapse * 7);
        case 'months':
            return ($timeToElapse * 30);
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
    $days = getDays($periodType, $timeToElapse);

    $bedsAvailable = ($totalHospitalBeds * 0.35);

    $impact = [];
    $impact['currentlyInfected'] = $reportedCases * 10;
    $impact['infectionsByRequestedTime'] = floor(intval($impact['currentlyInfected']) * $time);
    $impact['severeCasesByRequestedTime'] = floor(intval($impact['infectionsByRequestedTime']) * (15 / 100));//require hospitalization
    $beds = $bedsAvailable - intval($impact['severeCasesByRequestedTime']);
    if ($beds < 0) {
        $beds = $beds + 1;
    }
    $impact['hospitalBedsByRequestedTime'] = floor($beds);
    $impact['casesForICUByRequestedTime'] = floor(intval($impact['infectionsByRequestedTime']) * (5 / 100));//require icucare
    $impact['casesForVentilatorsByRequestedTime'] = floor(intval($impact['infectionsByRequestedTime']) * (2 / 100));//require ventilators

    $infected = intval($impact['infectionsByRequestedTime']);
    $earningPopulation = $avgDailyIncomePopulation * $population;
//    $impact['dollarsInFlight'] = round($infected * ($infected / $earningPopulation) * $avgDailyIncomeInUSD * $days);//economy loss
//    $impact['dollarsInFlight'] = round(($infected/$earningPopulation)  * $avgDailyIncomeInUSD * $days); //economy loss
    $impact['dollarsInFlight'] = round(($infected*$avgDailyIncomePopulation) * $avgDailyIncomeInUSD * $days); //economy loss

    $severeImpact = [];
    $severeImpact['currentlyInfected'] = $reportedCases * 50;
    $severeImpact['infectionsByRequestedTime'] = floor(intval($severeImpact['currentlyInfected']) * $time);
    $severeImpact['severeCasesByRequestedTime'] = floor(intval($severeImpact['infectionsByRequestedTime']) * (15 / 100));//require hospitalization
    $beds = $bedsAvailable - intval($severeImpact['severeCasesByRequestedTime']);
    if ($beds < 0) {
        $beds = $beds + 1;
    }
    $severeImpact['hospitalBedsByRequestedTime'] = floor($beds);
    $severeImpact['casesForICUByRequestedTime'] = floor(intval($severeImpact['infectionsByRequestedTime']) * (5 / 100));//require icucare
    $severeImpact['casesForVentilatorsByRequestedTime'] = floor(intval($severeImpact['infectionsByRequestedTime']) * (2 / 100));//require ventilators

    $infected = intval($severeImpact['infectionsByRequestedTime']);
    $earningPopulation = $avgDailyIncomePopulation * $population;
//    $severeImpact['dollarsInFlight'] = floor(($infected/$earningPopulation) * $avgDailyIncomeInUSD * $days);//economy loss
    $severeImpact['dollarsInFlight'] = floor(($infected*$avgDailyIncomePopulation) * $avgDailyIncomeInUSD * $days);//economy loss


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