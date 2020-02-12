<?php


namespace Dababo\LazyChunk\Tests\Helpers;



use PHPUnit\Framework\Assert;

class MockCallback {
    protected $invocations = 0;

    protected $finalInvocations = null;

    public static function mustBeCalled($times = 1, $action = null)
    {
        return (new static())->willBeCalled($times)->getCallable($action);
    }

    public function willBeCalled($times)
    {
        $this->finalInvocations = $times;

        return $this;
    }

    public function __destruct()
    {
        if ($this->finalInvocations !== null) {
            $this->assertHasBeenCalled($this->finalInvocations);
        }
    }

    public function getCallable(callable $action = null): callable
    {
        return function (...$args) use ($action) {
            $this->invocations++;

            if ($action !== null) {
                return $action(...$args);
            }
        };
    }

    public function assertHasBeenCalled($invocations = 1)
    {
        Assert::assertSame($invocations, $this->invocations, "The callback was not called correctly!");

        $this->invocations = 0;
    }
}