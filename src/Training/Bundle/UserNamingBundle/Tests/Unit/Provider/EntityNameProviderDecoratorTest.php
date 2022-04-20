<?php

namespace Training\Bundle\UserNamingBundle\Tests\Unit\Provider;

use Oro\Bundle\EntityBundle\Provider\EntityNameProviderInterface;
use Oro\Bundle\UserBundle\Entity\User;
use Training\Bundle\UserNamingBundle\Provider\EntityNameProviderDecorator;

class EntityNameProviderDecoratorTest extends \PHPUnit\Framework\TestCase
{

    /** @var EntityNameProviderDecorator */
    private $provider;

    /** @var EntityNameProviderInterface */
    private $decoratedProvider;

    protected function setUp(): void
    {
        $this->decoratedProvider = $this->createMock(EntityNameProviderInterface::class);

        $this->provider = new EntityNameProviderDecorator($this->decoratedProvider);
    }

    /**
     * @dataProvider nameProvider
     */
    public function testGetNameForUserEntity(User $entity, string|bool $expected)
    {
        $locale = null;
        $format = EntityNameProviderInterface::SHORT;

        $this->decoratedProvider->expects($this->never())
            ->method($this->anything());

        $this->assertSame($expected, $this->provider->getName($format, $locale, $entity));
    }

    public function nameProvider(): array
    {
        return [
            [(new User())->setLastName('Last')->setMiddleName('Middle'), 'Last Middle'],
            [(new User())->setLastName('Last')->setFirstName('First')->setMiddleName('Middle'), 'Last First Middle'],
            [(new User())->setLastName('Last')->setFirstName('First'), 'Last First'],
            [(new User())->setMiddleName('Middle')->setFirstName('First'), 'First Middle'],
            [new User(), false]
        ];
    }

    public function testGetNameFornonUserEntity()
    {
        $locale = null;
        $format = EntityNameProviderInterface::SHORT;
        $entity = new \stdClass();
        $expected = 'some string';

        $this->decoratedProvider->expects($this->once())
            ->method('getName')
            ->with($format, $locale, $entity)
            ->willReturn($expected);

        $this->assertSame($expected, $this->provider->getName($format, $locale, $entity));
    }
}
