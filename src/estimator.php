<?php


function getTime($periodType, $timeToElapse)
{
    //pow(2,10)=30 days
    switch ($periodType) {
        case 'days':
            return ($timeToElapse * (pow(2, 10))) / 30;
        case 'weeks':
            return ($timeToElapse * 7 * (pow(2, 10))) / 30;
        case 'months':
            return ($timeToElapse * 30 * (pow(2, 10))) / 30;
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

function doOutput(){

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

    $impact = [];
    $impact['currentlyInfected'] = $reportedCases * 10;
    $impact['infectionsByRequestedTime'] = round(intval($impact['currentlyInfected']) * $time);
    $severeImpact['dollarsInFlight'] = round((intval($impact['infectionsByRequestedTime']) * $avgDailyIncomePopulation) * $avgDailyIncomeInUSD * 30);//economy loss 30 days


    $severeImpact = [];
    $severeImpact['currentlyInfected'] = $reportedCases * 50;
    $severeImpact['infectionsByRequestedTime'] = round(intval($severeImpact['currentlyInfected']) * $time);
    $severeImpact['severeCasesByRequestedTime'] = round(intval($severeImpact['infectionsByRequestedTime']) * (15 / 100));//require hospitalization

    $occupiedBeds = (65 / 100) * $totalHospitalBeds;
    $bedsAvailable = $totalHospitalBeds - $occupiedBeds;

    $severeImpact['hospitalBedsByRequestedTime'] = round(intval($severeImpact['severeCasesByRequestedTime']) - $bedsAvailable);
    $severeImpact['casesForICUByRequestedTime'] = round(intval($severeImpact['infectionsByRequestedTime']) * (5 / 100));//require icucare
    $severeImpact['casesForVentilatorsByRequestedTime'] = round(intval($severeImpact['infectionsByRequestedTime']) * (5 / 100));//require ventilators


    $severeImpact['dollarsInFlight'] = round((intval($severeImpact['infectionsByRequestedTime']) * $avgDailyIncomePopulation) * $avgDailyIncomeInUSD * 30);//economy loss 30 days


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

function array_to_xml($student_info, &$xml_student_info) {
    foreach($student_info as $key => $value) {
        if(is_array($value)) {
            $key = is_numeric($key) ? "item$key" : $key;
            $subnode = $xml_student_info->addChild("$key");
            array_to_xml($value, $subnode);
        }
        else {
            $key = is_numeric($key) ? "item$key" : $key;
            $xml_student_info->addChild("$key","$value");
        }
    }
}

function test()
{
    $data['region'] = [
        'name' => 'Africa',
        'avgAge' => 19.7,
        'avgDailyIncomeInUSD' => 19.7,
        'avgDailyIncomePopulation' => 19.7,
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