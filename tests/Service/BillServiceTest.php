<?php


namespace App\Tests\Service;


use App\Entity\Reservation;
use App\Enum\RoomType;
use App\Enum\UserCategoryType;
use App\Service\BillService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BillServiceTest extends WebTestCase
{
    public function testNormalReservation()
    {
        self::bootKernel();

        $billService = self::$container->get(BillService::class);

        $reservation = new Reservation();
        $reservation->setStart(new \DateTime("31.12.2018 17:00"));
        $reservation->setEnd(new \DateTime("31.12.2018 19:00"));
        $reservation->setRoom(RoomType::MM_A_98);

        $reservationModels = $billService->getReservations([$reservation], UserCategoryType::STUDENT, $reservationTotal);

        $this->assertEquals(2, $reservationTotal, "price for normal room & student should be CHF 1 per hour");
        $this->assertCount(1, $reservationModels);
    }

    public function testManyNormalReservations()
    {
        self::bootKernel();

        $billService = self::$container->get(BillService::class);

        $reservation1 = new Reservation();
        $reservation1->setStart(new \DateTime("31.12.2018 23:00"));
        $reservation1->setEnd(new \DateTime("01.01.2019 02:00"));
        $reservation1->setRoom(RoomType::MM_A_71_3);

        $reservation2 = new Reservation();
        $reservation2->setStart(new \DateTime("31.12.2018 05:00"));
        $reservation2->setEnd(new \DateTime("31.12.2018 19:00"));
        $reservation2->setRoom(RoomType::MM_A_98);

        $reservation3 = new Reservation();
        $reservation3->setStart(new \DateTime("30.12.2018 22:00"));
        $reservation3->setEnd(new \DateTime("31.12.2018 19:00"));
        $reservation3->setRoom(RoomType::HPI_D_4_1);

        $reservationModels = $billService->getReservations([$reservation1, $reservation2, $reservation3], UserCategoryType::STUDENT, $reservationTotal);

        $this->assertEquals(3 + 14 + 21, $reservationTotal, "prices for normal rooms & student should be CHF 1 per hour");
        $this->assertCount(3, $reservationModels);
    }
}
