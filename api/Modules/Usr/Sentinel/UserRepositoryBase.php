<?php namespace Modules\Usr\Sentinel;

use Carbon\Carbon;
use Cartalyst\Sentinel\Hashing\NativeHasher;
//use Cartalyst\Sentinel\Users\IlluminateUserRepository;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use DB;

// Refer: Cartalyst\Sentinel\Users\IlluminateUserRepository;
class UserRepositoryBase extends EloquentBaseRepository {
    /**
     * The Hasher instance.
     *
     * @var \Cartalyst\Sentinel\Hashing\HasherInterface
     */
    protected $hasher;

    public function __construct($model) {
        parent::__construct($model);

        $this->hasher = new NativeHasher();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?UserInterface {
        return $this->getModel()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCredentials(array $credentials): ?UserInterface {
        if (empty($credentials)) {
            return null;
        }

        $instance = $this->getModel();

        $loginNames = $instance->getLoginNames();

        [$logins] = $this->parseCredentials($credentials, $loginNames);

        if (empty($logins)) {
            return null;
        }

        $query = $instance->newQuery();

        if (is_array($logins)) {
            foreach ($logins as $key => $value) {
                $query->where($key, $value);
            }
        } else {
            $query->whereNested(function($query) use ($loginNames, $logins) {
                foreach ($loginNames as $index => $name) {
                    $method = $index === 0 ? 'where' : 'orWhere';

                    $query->{$method}($name, $logins);
                }
            });
        }

        return $query->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByPersistenceCode(string $code): ?UserInterface {
        return $this->getModel()
            ->newQuery()
            ->whereHas('persistences', function($q) use ($code) {
                $q->where('code', $code);
            })
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function recordLogin(UserInterface $user): bool {
        $user->last_login = Carbon::now();

        return (bool)$user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function recordLogout(UserInterface $user): bool {
        return (bool)$user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(UserInterface $user, array $credentials): bool {
        return $this->hasher->check($credentials['password'], $user->password);
    }

    /**
     * Parses the given credentials to return logins, password and others.
     *
     * @param array $credentials
     * @param array $loginNames
     * @throws \InvalidArgumentException
     * @return array
     */
    protected function parseCredentials(array $credentials, array $loginNames): array {
        if (isset($credentials['password'])) {
            $password = $credentials['password'];

            unset($credentials['password']);
        } else {
            $password = null;
        }

        $passedNames = array_intersect_key($credentials, array_flip($loginNames));

        if (count($passedNames) > 0) {
            $logins = [];

            foreach ($passedNames as $name => $value) {
                $logins[$name] = $credentials[$name];
                unset($credentials[$name]);
            }
        } else if (isset($credentials['login'])) {
            $logins = $credentials['login'];
            unset($credentials['login']);
        } else {
            $logins = [];
        }

        return [$logins, $password, $credentials];
    }
}
