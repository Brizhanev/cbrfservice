<?php

namespace marvin255\cbrfservice\tests;

use marvin255\cbrfservice\CbrfDaily;

class CbrfDailyTest extends BaseTestCase
{
    public function testDefaultClien()
    {
        $service = new CbrfDaily;

        $this->assertInstanceOf('\SoapClient', $service->getSoapClient());
    }

    public function testGetCursOnDateCurrency()
    {
        list($courses, $response) = $this->getCoursesFixture();
        $time = time();

        $soapClient = $this->getMockBuilder('\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->equalTo('GetCursOnDate'),
                $this->equalTo([['On_date' => date('Y-m-d\TH:i:s', $time)]])
            )
            ->will($this->returnValue($response));

        $service = new CbrfDaily($soapClient);

        $this->assertSame(
            $courses,
            $service->GetCursOnDate(date('d.m.Y H:i:s', $time)),
            'all list'
        );
        $this->assertSame(
            $courses[2],
            $service->GetCursOnDate(date('d.m.Y H:i:s', $time), 'VchCode_2'),
            'by VchCode'
        );
        $this->assertSame(
            $courses[1],
            $service->GetCursOnDate(date('Y-m-d\TH:i:sP', $time), 'Vname_1'),
            'by Vname'
        );
        $this->assertSame(
            $courses[0],
            $service->GetCursOnDate($time, 'Vcode_0'),
            'by Vcode'
        );
    }

    public function testGetCursOnDateException()
    {
        $exceptionMessage = 'exception_message_' . mt_rand();
        $soapClient = $this->getMockBuilder('\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('GetCursOnDate'))
            ->will($this->throwException(new \Exception($exceptionMessage)));

        $service = new CbrfDaily($soapClient);

        $this->setExpectedException('\marvin255\cbrfservice\Exception', $exceptionMessage);
        $service->GetCursOnDate();
    }

    public function testGetCursOnDateWrongDateException()
    {
        $exceptionDate = 'wrong_date_' . mt_rand();
        $soapClient = $this->getMockBuilder('\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')->with($this->equalTo('GetCursOnDate'));

        $service = new CbrfDaily($soapClient);

        $this->setExpectedException('\InvalidArgumentException', $exceptionDate);
        $service->GetCursOnDate($exceptionDate);
    }

    public function testEnumValutes()
    {
        list($courses, $response) = $this->getEnumValutesFixture();

        $soapClient = $this->getMockBuilder('\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with(
                $this->equalTo('EnumValutes'),
                $this->equalTo([['Seld' => false]])
            )
            ->will($this->returnValue($response));

        $service = new CbrfDaily($soapClient);

        $this->assertSame(
            $courses,
            $service->EnumValutes(false),
            'all list'
        );
        $this->assertSame(
            $courses[2],
            $service->EnumValutes(false, 'VcommonCode_2'),
            'by VcommonCode'
        );
        $this->assertSame(
            $courses[1],
            $service->EnumValutes(false, 'VcharCode_1'),
            'by VcharCode'
        );
        $this->assertSame(
            $courses[0],
            $service->EnumValutes(false, 'Vname_0'),
            'by Vname'
        );
        $this->assertSame(
            $courses[2],
            $service->EnumValutes(false, 'Vcode_2'),
            'by Vcode'
        );
    }

    public function testEnumValutesException()
    {
        $exceptionMessage = 'exception_message_' . mt_rand();
        $soapClient = $this->getMockBuilder('\SoapClient')
            ->disableOriginalConstructor()
            ->getMock();
        $soapClient->method('__soapCall')
            ->with($this->equalTo('EnumValutes'))
            ->will($this->throwException(new \Exception($exceptionMessage)));

        $service = new CbrfDaily($soapClient);

        $this->setExpectedException('\marvin255\cbrfservice\Exception', $exceptionMessage);
        $service->EnumValutes();
    }

    protected function getCoursesFixture()
    {
        $courses = [];
        for ($i = 0; $i <= 3; $i++) {
            $courses[] = [
                'VchCode' => "VchCode_{$i}",
                'Vname' => "Vname_{$i}",
                'Vcode' => "Vcode_{$i}",
                'Vcurs' => floatval(mt_rand()),
                'Vnom' => floatval(mt_rand()),
            ];
        }

        $soapResponse = new \stdClass;
        $soapResponse->GetCursOnDateResult = new \stdClass;

        $soapResponse->GetCursOnDateResult->any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $soapResponse->GetCursOnDateResult->any .= '<ValuteData xmlns="">';
        foreach ($courses as $course) {
            $soapResponse->GetCursOnDateResult->any .= '<ValuteCursOnDate xmlns="">';
            foreach ($course as $key => $value) {
                $soapResponse->GetCursOnDateResult->any .= "<{$key}>{$value}</{$key}>";
            }
            $soapResponse->GetCursOnDateResult->any .= '</ValuteCursOnDate>';
        }
        $soapResponse->GetCursOnDateResult->any .= '</ValuteData>';
        $soapResponse->GetCursOnDateResult->any .= '</diffgr:diffgram>';

        return [$courses, $soapResponse];
    }

    protected function getEnumValutesFixture()
    {
        $courses = [];
        for ($i = 0; $i <= 3; $i++) {
            $courses[] = [
                'Vcode' => "Vcode_{$i}",
                'Vname' => "Vname_{$i}",
                'VEngname' => "VEngname_{$i}",
                'Vnom' => "Vnom_{$i}",
                'VcommonCode' => "VcommonCode_{$i}",
                'VnumCode' => "VnumCode_{$i}",
                'VcharCode' => "VcharCode_{$i}",
            ];
        }

        $soapResponse = new \stdClass;
        $soapResponse->EnumValutesResult = new \stdClass;

        $soapResponse->EnumValutesResult->any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        $soapResponse->EnumValutesResult->any .= '<ValuteData xmlns="">';
        foreach ($courses as $course) {
            $soapResponse->EnumValutesResult->any .= '<EnumValutes xmlns="">';
            foreach ($course as $key => $value) {
                $soapResponse->EnumValutesResult->any .= "<{$key}>{$value}</{$key}>";
            }
            $soapResponse->EnumValutesResult->any .= '</EnumValutes>';
        }
        $soapResponse->EnumValutesResult->any .= '</ValuteData>';
        $soapResponse->EnumValutesResult->any .= '</diffgr:diffgram>';

        return [$courses, $soapResponse];
    }
}
