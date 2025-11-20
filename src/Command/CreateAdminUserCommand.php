<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin user',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Create Admin User');

        $helper = $this->getHelper('question');

        // Ask for email
        $emailQuestion = new Question('Email: ');
        $emailQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Invalid email address');
            }
            
            // Check if user already exists
            $existingUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $answer]);
                
            if ($existingUser) {
                throw new \RuntimeException('User with this email already exists');
            }

            return $answer;
        });
        $email = $helper->ask($input, $output, $emailQuestion);

        // Ask for password
        $passwordQuestion = new Question('Password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $passwordQuestion->setValidator(function ($answer) {
            if (strlen($answer) < 6) {
                throw new \RuntimeException('Password must be at least 6 characters');
            }
            return $answer;
        });
        $password = $helper->ask($input, $output, $passwordQuestion);

        // Confirm password
        $confirmPasswordQuestion = new Question('Confirm Password: ');
        $confirmPasswordQuestion->setHidden(true);
        $confirmPasswordQuestion->setHiddenFallback(false);
        $confirmPassword = $helper->ask($input, $output, $confirmPasswordQuestion);

        if ($password !== $confirmPassword) {
            $io->error('Passwords do not match!');
            return Command::FAILURE;
        }

        // Create admin user
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success([
            'Admin user created successfully!',
            sprintf('Email: %s', $email),
            'Roles: ROLE_ADMIN, ROLE_USER',
            '',
            'You can now login at:',
            '- Admin Panel: http://localhost:8080/admin',
            '- API: POST http://localhost:8080/api/v1/login_check',
        ]);

        return Command::SUCCESS;
    }
}

