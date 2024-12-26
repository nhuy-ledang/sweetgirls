<?php namespace Modules\Usr\Sentinel;

use Cartalyst\Sentinel\Cookies\CookieInterface;
use Cartalyst\Sentinel\Persistences\PersistableInterface;
use Cartalyst\Sentinel\Sessions\SessionInterface;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

// Refer: Cartalyst\Sentinel\Persistences\IlluminatePersistenceRepository
class PersistenceRepository extends EloquentBaseRepository implements PersistenceRepositoryInterface {
    /**
     * Single session.
     *
     * @var bool
     */
    protected $single = false;

    /**
     * Session storage driver.
     *
     * @var \Cartalyst\Sentinel\Sessions\SessionInterface
     */
    protected $session;

    /**
     * Cookie storage driver.
     *
     * @var \Cartalyst\Sentinel\Cookies\CookieInterface
     */
    protected $cookie;

    /**
     * Create a new Sentinel persistence repository.
     *
     * @param \Cartalyst\Sentinel\Sessions\SessionInterface $session
     * @param \Cartalyst\Sentinel\Cookies\CookieInterface $cookie
     * @param null $model
     * @param bool $single
     * @return void
     */
    /**
     * PersistenceRepository constructor.
     *
     * @param SessionInterface $session
     * @param CookieInterface $cookie
     * @param null $model
     * @param bool $single
     */
    public function __construct(SessionInterface $session, CookieInterface $cookie, $model = null, bool $single = false) {
        $this->session = $session;
        $this->cookie = $cookie;
        $this->single = $single;

        parent::__construct($model);
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ?string {
        if ($code = $this->session->get()) {
            return $code;
        }

        if ($code = $this->cookie->get()) {
            return $code;
        }

        return null;
    }

    /**
     * Finds a persistence by persistence code.
     *
     * @param string $code
     * @return PersistenceInterface|null
     */
    public function findByPersistenceCode(string $code): ?PersistenceInterface {
        return $this->getModel()->where('code', $code)->first();
    }

    /**
     * Adds a new user persistence to the current session and attaches the user.
     *
     * @param PersistableInterface $persistable
     * @return bool
     */
    public function persist(PersistableInterface $persistable): bool {
        if ($this->single) {
            $this->flush($persistable);
        }

        $code = $persistable->generatePersistenceCode();

        $persistence = $this->model->newInstance();

        $persistence->{$persistable->getPersistableKey()} = $persistable->getPersistableId();

        $persistence->code = $code;

        return $persistence->save();
    }

    /**
     * {@inheritdoc}
     */
    public function forget(): ?bool {
        $code = $this->check();

        if ($code === null) {
            return null;
        }

        $this->session->forget();
        $this->cookie->forget();

        return $this->remove($code);
    }

    /**
     * Removes the given persistence code.
     *
     * @param string $code
     * @return bool|null
     */
    public function remove(string $code): ?bool {
        return $this->getModel()->where('code', $code)->delete();
    }

    /**
     * Flushes persistences for the given user.
     *
     * @param PersistableInterface $persistable
     * @return void
     */
    public function flush(PersistableInterface $persistable): void {
        $relationship = $persistable->getPersistableRelationship();

        foreach ($persistable->{$relationship}()->get() as $persistence) {
            $persistence->delete();
        }
    }
}
