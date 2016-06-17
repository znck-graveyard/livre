<?php namespace Znck\Livre\Providers;

use Znck\Livre\Contracts\Provider as ProviderContract;

abstract class Provider implements ProviderContract
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        if (empty($this->name)) {
            $this->name = $this->getDefaultName();
        }

        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Transform request parameters using the provided map.
     *
     * @param $query
     * @param $map
     *
     * @return array
     */
    protected function transformQueryParameters($query, $map)
    {
        $processed = [];
        $ignored = [];
        foreach ($query as $key => $value) {
            if (! empty($value)) {
                if (array_key_exists($key, $map)) {
                    $processed[$map[$key]] = $value;
                } else {
                    $ignored[$key] = $value;
                }
            }
        }

        return [$processed, $ignored];
    }

    protected function transformBookAttributes(array $attributes, array $map)
    {
        $processed = $attributes;

        foreach (array_dot($map) as $key => $replace) {
            $value = array_get($processed, $key);
            if (! empty($value)) {
                $processed[$replace] = $value;
                array_forget($processed, $key);
            }
        }

        return $processed;
    }
}
