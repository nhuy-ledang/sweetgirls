<?php namespace Modules\Usr\Sentinel;

use Cartalyst\Sentinel\Persistences\PersistableInterface;
use Modules\Core\Repositories\BaseRepository;

interface PersistenceRepositoryInterface extends BaseRepository {
    /**
     * Finds a persistence by persistence code.
     *
     * @param string $code
     * @return PersistenceInterface|null
     */
    public function findByPersistenceCode(string $code): ?PersistenceInterface;

    /**
     * Adds a new user persistence to the current session and attaches the user.
     *
     * @param PersistableInterface $persistable
     * @return bool
     */
    public function persist(PersistableInterface $persistable): bool;

    /**
     * Removes the given persistence code.
     *
     * @param string $code
     * @return bool|null
     */
    public function remove(string $code): ?bool;

    /**
     * Flushes persistences for the given user.
     *
     * @param PersistableInterface $persistable
     * @return void
     */
    public function flush(PersistableInterface $persistable): void;
}
