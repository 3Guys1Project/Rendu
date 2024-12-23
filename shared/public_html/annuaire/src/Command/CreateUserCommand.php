<?php
// src/Command/CreateUserCommand.php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserManager            $userManager,
        private readonly UserRepository         $userRepository
    )
    {
        parent::__construct();
    }

    public static function getDefaultName(): string
    {
        return 'app:create-user';
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the user.')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the user.')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the user.')
            ->addArgument('visible', InputArgument::OPTIONAL, 'The visibility of the user.')
            ->addArgument('code', InputArgument::OPTIONAL, 'The code of the user.')
            ->addArgument('admin', InputArgument::OPTIONAL, 'Is the user an admin?');
    }

    /**
     * @throws RandomException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $username = $input->getArgument('username');
        if (!$username) {
            $question = new Question('Please enter the username: ');
            $username = $helper->ask($input, $output, $question);
        }

        $email = $input->getArgument('email');
        if (!$email) {
            $question = new Question('Please enter the email: ');
            $email = $helper->ask($input, $output, $question);
        }

        $password = $input->getArgument('password');
        if (!$password) {
            $question = new Question('Please enter the password: ');
            $password = $helper->ask($input, $output, $question);
        }

        $visible = $input->getArgument('visible');
        if (!$visible) {
            $question = new Question('Is the user visible (true/false)? ', 'true');
            $visible = $helper->ask($input, $output, $question);
        }

        $code = $input->getArgument('code');
        if (!$code) {
            $question = new Question('Please enter the code (or leave blank to generate one): ');
            $code = $helper->ask($input, $output, $question);
            if (!$code) {
                $code = $this->generateUniqueCode();
            }
        } else {
            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{20}$/', $code)) {
                $output->writeln('Invalid code. The code must contain at least one letter, one digit, and be 20 characters long.');
                return Command::FAILURE;
            }

            if ($this->userRepository->findOneBy(['code' => $code])) {
                $output->writeln('The code already exists.');
                return Command::FAILURE;
            }
        }

        $admin = $input->getArgument('admin');
        if (!$admin) {
            $question = new Question('Is the user an admin (true/false)? ', 'false');
            $admin = $helper->ask($input, $output, $question);
        }

        // Validate arguments
        if (!preg_match('/^[a-zA-Z0-9_-]{3,30}$/', $username)) {
            $output->writeln('Invalid username. The username must be 3-30 characters long and can contain letters, numbers, underscores, and hyphens.');
            return Command::FAILURE;
        }

        if (!preg_match('/^[\w\-.]+@([\w-]+\.)+[\w-]{2,4}$/', $email)) {
            $output->writeln('Invalid email format.');
            return Command::FAILURE;
        }

        if (!preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$#', $password)) {
            $output->writeln('Invalid password. The password must be 8-30 characters long and contain at least one uppercase letter, one lowercase letter, and one digit.');
            return Command::FAILURE;
        }

        if (!in_array($visible, ['true', 'false'], true)) {
            $output->writeln('Invalid input type for visible. It should be "true" or "false".');
            return Command::FAILURE;
        }

        $visible = $visible === 'true';

        $user = $this->userRepository->getUserByUsernameOrEmail($username, $email);

        if (!empty($user)) {
            $output->writeln($user);
            $output->writeln('User already exists!');
            return Command::FAILURE;
        } else {
            $output->writeln('Creating user...');
        }

        $user = new User();

        $this->userManager->processNewUser($user, $password);

        $user->setVisible($visible);
        $user->setLastLogin(new \DateTime());
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setCode($code);

        if ($admin === 'true') {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User successfully created!');

        return Command::SUCCESS;
    }

    /**
     * @throws RandomException
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = $this->generateCode();
        } while (!empty($this->userRepository->getUserByCode($code)));

        return $code;
    }

    /**
     * @throws RandomException
     */
    private function generateCode(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < 20; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        // Ensure the code contains at least one letter and one digit
        if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{20}$/', $code)) {
            return $this->generateCode();
        }
        return $code;
    }
}