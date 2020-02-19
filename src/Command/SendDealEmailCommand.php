<?php

namespace App\Command;

use App\Entity\Deal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendDealEmailCommand extends Command
{
    protected static $defaultName = 'deal:emails:send';

    protected $mailer;
    protected $templating;
    protected $entityManager;

    public function __construct(string $name = null, \Swift_Mailer $mailer, \Twig_Environment $templating, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);

        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $deals = $this->entityManager->getRepository('App\Entity\Deal')->findBy(['statusEmail' => Deal::STATUS_EMAIL_NOT_SENT]);

        /** @var Deal $deal */
        foreach ($deals as $deal) {
            $message = (new \Swift_Message('Platforma Alter Investment S.A'))
                ->setFrom('kontakt@alterinvestment.pl')
                ->setTo($deal->getEmail())
                ->setBody(
                    $this->templating->render(
                        'emails/new_deal.html.twig',
                        ['deal' => $deal,]
                ),
                'text/html'
                );

            $files = $deal->getFiles();
            if (count($files)) {
                foreach ($files as $file) {
                    $path = preg_replace('#http.*?\/docs\/#', './public/docs/', $file);
                    $message->attach(\Swift_Attachment::fromPath($path));
                }
            }

            $this->mailer->send($message);

            $deal->setStatusEmail(Deal::STATUS_EMAIL_SENT);

            $this->entityManager->flush();
        }

        return 0;
    }
}
