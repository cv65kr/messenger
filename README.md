# CQRS + Event Sourcing for Symfony

This is simple implementation CQRS + Event Sourcing for Symfony.

## Installation

```bash
composer require cv65kr/messenger
```

Activate in `config/bundles.php`:

```php
Messenger\MessengerBundle::class => ['all' => true],
```

Update database (migration in future):

```bash
bin/console d:s:u --force --dump-sql
```

To activate async bus, open `config/packages/messenger.yaml` and paste:

```yaml
framework:
    messenger:
        transports:
            events: "%env(MESSENGER_TRANSPORT_DSN)%"

        routing:
            'Messenger\Event\EventInterface': events
```

## How to use

The current example: [https://github.com/cv65kr/symfony-4-es-cqrs-boilerplate/tree/feature/es](https://github.com/cv65kr/symfony-4-es-cqrs-boilerplate/tree/feature/es)


### Aggregate root

```php
class User extends AggregateRoot
{
    /** @var UuidInterface */
    private $uuid;

    /** @var Email */
    private $email;

    /** @var HashedPassword */
    private $hashedPassword;

    /** @var DateTime */
    private $createdAt;

    /** @var DateTime|null */
    private $updatedAt;

    public static function create(
        UuidInterface $uuid,
        Credentials $credentials,
        UniqueEmailSpecificationInterface $uniqueEmailSpecification
    ): self {
        $uniqueEmailSpecification->isUnique($credentials->email);

        $user = new self();

        $user->apply(new UserWasCreated($uuid, $credentials, DateTime::now()));

        return $user;
    }

    private function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    private function setHashedPassword(HashedPassword $hashedPassword): void
    {
        $this->hashedPassword = $hashedPassword;
    }

    private function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    private function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function createdAt(): string
    {
        return $this->createdAt->toString();
    }

    public function updatedAt(): ?string
    {
        return isset($this->updatedAt) ? $this->updatedAt->toString() : null;
    }

    public function email(): string
    {
        return $this->email->toString();
    }

    public function uuid(): string
    {
        return $this->uuid->toString();
    }

    public function getAggregateRootId(): AggregateRootId
    {
        return AggregateRootId::fromUUID($this->uuid);
    }

    protected function applyUserWasCreated(UserWasCreated $event): void
    {
        $this->uuid = $event->uuid;

        $this->setEmail($event->credentials->email);
        $this->setHashedPassword($event->credentials->password);
        $this->setCreatedAt($event->createdAt);
    }

}
```

### Event Sourcing Repository

```php
final class UserStore extends EventSourcingRepository implements UserRepositoryInterface
{
    public function store(User $user): void
    {
        $this->save($user);
    }

    public function get(UuidInterface $uuid): User
    {
        /** @var User $user */
        $user = $this->load(AggregateRootId::fromUUID($uuid));

        return $user;
    }

    public function getAggregateRoot(): string
    {
        return User::class;
    }
}
```

### Projection

#### Read model

```php
class UserView implements ReadModelInterface
{
    /** @var UuidInterface */
    private $uuid;

    /** @var Credentials */
    private $credentials;

    /** @var DateTime */
    private $createdAt;

    /** @var DateTime */
    private $updatedAt;

    public static function fromSerializable(EventInterface $event): self
    {
        return self::deserialize($event->serialize());
    }

    public static function deserialize(array $data): self
    {
        $instance = new self();

        $instance->uuid = Uuid::fromString($data['uuid']);
        $instance->credentials = new Credentials(
            Email::fromString($data['credentials']['email']),
            HashedPassword::fromHash($data['credentials']['password'] ?? '')
        );

        $instance->createdAt = DateTime::fromString($data['created_at']);
        $instance->updatedAt = isset($data['updated_at']) ? DateTime::fromString($data['updated_at']) : null;

        return $instance;
    }

    public function serialize(): array
    {
        return [
            'uuid' => $this->getId(),
            'credentials' => [
                'email' => (string) $this->credentials->email,
            ],
        ];
    }

    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function email(): string
    {
        return (string) $this->credentials->email;
    }

    public function changeEmail(Email $email): void
    {
        $this->credentials->email = $email;
    }

    public function changeUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function hashedPassword(): string
    {
        return (string) $this->credentials->password;
    }

    public function getId(): string
    {
        return $this->uuid->toString();
    }
}
```

#### Projector

```php
class UserProjectionFactory extends Projector
{
    /** @var MysqlUserReadModelRepository */
    private $repository;

    public function __construct(MysqlUserReadModelRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function applyUserWasCreated(UserWasCreated $userWasCreated): void
    {
        $userReadModel = UserView::fromSerializable($userWasCreated);

        $this->repository->add($userReadModel);
    }

    protected function applyUserEmailChanged(UserEmailChanged $emailChanged): void
    {
        /** @var UserView $userReadModel */
        $userReadModel = $this->repository->oneByUuid($emailChanged->uuid);

        $userReadModel->changeEmail($emailChanged->email);
        $userReadModel->changeUpdatedAt($emailChanged->updatedAt);

        $this->repository->apply();
    }
}
```

### Consume events from queue

```php
class SendEventsToElasticConsumer implements EventHandlerInterface
{
    /** @var EventElasticRepository */
    private $eventElasticRepository;

    public function __construct(EventElasticRepository $eventElasticRepository)
    {
        $this->eventElasticRepository = $eventElasticRepository;
    }

    public function __invoke(EventInterface $event): void
    {
        $this->eventElasticRepository->store($event);
    }
}
```