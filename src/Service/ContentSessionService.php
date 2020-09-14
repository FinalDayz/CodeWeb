<?php


namespace App\Service;


use App\Entity\Session;
use App\Entity\SessionContent;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ContentSessionService
{
    static $ID_NUMBER_OF_BLOCKS = 2;
    static $ID_HEX_PER_BLOCK = 3;
    // Number of combinations = 16 ^ ($ID_NUMBER_OF_BLOCKS * $ID_HEX_PER_BLOCK)
    // = 16 ^ (3*2) = 16 581 375

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSession(string $sessionId, bool $mayExists = true): Session
    {
        $session = null;
        if ($mayExists) {
           $session = $this->entityManager->getRepository(Session::class)
               ->findOneBy(['sessionId' => $sessionId]);
        }

        if(!$session | !$mayExists) {
            $session = new Session();
            $session->setSessionId($sessionId);
            $this->entityManager->persist($session);
            $this->entityManager->flush();
        }

        return $session;
    }

    /**
     * @param Session $session
     * @return array
     */
    public function getSessionContent(Session $session): array
    {
        /** @var SessionContent[] $contentArr */
        $contentArr = $this->entityManager->getRepository(SessionContent::class)
            ->findBy(['session' => $session]);
        $contentAssociativeArray = [];

        foreach($contentArr as $content) {
            $type = $content->getType();;
            if(!isset($contentAssociativeArray[$type])) {
                $contentAssociativeArray[$type] = [];
            }
            array_push($contentAssociativeArray[$type], $content);
        }

        return $contentAssociativeArray;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function generateId(): string
    {
        $hex = [];
        for($i = 0; $i < self::$ID_NUMBER_OF_BLOCKS; $i++) {
            $longHex = bin2hex(random_bytes(self::$ID_HEX_PER_BLOCK));
            array_push(
                $hex,
                substr ($longHex, 0, self::$ID_HEX_PER_BLOCK)
            );
        }

        return implode($hex, '-');
    }
}