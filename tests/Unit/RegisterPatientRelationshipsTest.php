<?php

namespace Tests\Unit;

use App\Console\Commands\RegisterPatientRelationships;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class RegisterPatientRelationshipsTest extends TestCase
{
    private RegisterPatientRelationships $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new RegisterPatientRelationships;
    }

    public function test_extracts_spouse_relationship_correctly(): void
    {
        $identifier = '123-45-6789-SPOUSE';
        $result = $this->callPrivateMethod('extractRelationshipFromIdentifier', [$identifier]);

        $this->assertNotNull($result);
        $this->assertEquals('123-45-6789', $result['base_identifier']);
        $this->assertEquals('SPOUSE', $result['relationship_raw']);
        $this->assertEquals('SPOUSE', $result['relationship_code']);
        $this->assertEquals('Spouse', $result['relationship_display']);
    }

    public function test_extracts_child_relationship_correctly(): void
    {
        $identifier = '987-65-4321-CHILD';
        $result = $this->callPrivateMethod('extractRelationshipFromIdentifier', [$identifier]);

        $this->assertNotNull($result);
        $this->assertEquals('987-65-4321', $result['base_identifier']);
        $this->assertEquals('CHILD', $result['relationship_raw']);
        $this->assertEquals('CHILD', $result['relationship_code']);
        $this->assertEquals('Child', $result['relationship_display']);
    }

    public function test_normalizes_numbered_child_relationships(): void
    {
        $testCases = [
            ['111-22-3333-CHILD2', 'CHILD2', 'CHILD'],
            ['111-22-3333-CHILD3', 'CHILD3', 'CHILD'],
            ['111-22-3333-CHILD4', 'CHILD4', 'CHILD'],
            ['111-22-3333-CHILD5', 'CHILD5', 'CHILD'],
        ];

        foreach ($testCases as [$identifier, $expectedRaw, $expectedCode]) {
            $result = $this->callPrivateMethod('extractRelationshipFromIdentifier', [$identifier]);

            $this->assertNotNull($result, "Failed for identifier: {$identifier}");
            $this->assertEquals('111-22-3333', $result['base_identifier']);
            $this->assertEquals($expectedRaw, $result['relationship_raw']);
            $this->assertEquals($expectedCode, $result['relationship_code']);
            $this->assertEquals('Child', $result['relationship_display']);
        }
    }

    public function test_handles_various_relationship_types(): void
    {
        $testCases = [
            ['123-45-6789-PARENT', 'PARENT', 'PARENT', 'Parent'],
            ['123-45-6789-SIBLING', 'SIBLING', 'SIBLING', 'Sibling'],
            ['123-45-6789-GRANDPARENT', 'GRANDPARENT', 'GRANDPRN', 'Grandparent'],
            ['123-45-6789-UNCLE', 'UNCLE', 'UNCLE', 'Uncle'],
            ['123-45-6789-AUNT', 'AUNT', 'AUNT', 'Aunt'],
            ['123-45-6789-FRIEND', 'FRIEND', 'FRND', 'Friend'],
            ['123-45-6789-GUARDIAN', 'GUARDIAN', 'GUARD', 'Guardian'],
        ];

        foreach ($testCases as [$identifier, $expectedRaw, $expectedCode, $expectedDisplay]) {
            $result = $this->callPrivateMethod('extractRelationshipFromIdentifier', [$identifier]);

            $this->assertNotNull($result, "Failed for identifier: {$identifier}");
            $this->assertEquals('123-45-6789', $result['base_identifier']);
            $this->assertEquals($expectedRaw, $result['relationship_raw']);
            $this->assertEquals($expectedCode, $result['relationship_code']);
            $this->assertEquals($expectedDisplay, $result['relationship_display']);
        }
    }

    public function test_returns_null_for_invalid_identifiers(): void
    {
        $invalidIdentifiers = [
            '123-45-6789',           // Sin relación
            '123-45',                // Muy corto
            '123',                   // Muy corto
            '123-45-6789-INVALID',   // Relación no reconocida
            '123-45-6789-',          // Relación vacía
            '',                      // Vacío
        ];

        foreach ($invalidIdentifiers as $identifier) {
            $result = $this->callPrivateMethod('extractRelationshipFromIdentifier', [$identifier]);
            $this->assertNull($result, "Should return null for identifier: {$identifier}");
        }
    }

    public function test_handles_different_identifier_formats(): void
    {
        $testCases = [
            'PE-123-456-SPOUSE',      // Panamá con prefijo
            '8-123-456-CHILD',        // Panamá estándar
            '123-456-789-PARENT',     // Formato genérico
            'A-B-C-SIBLING',          // Con letras
        ];

        foreach ($testCases as $identifier) {
            $result = $this->callPrivateMethod('extractRelationshipFromIdentifier', [$identifier]);

            // Todos estos deberían funcionar ya que tienen el formato correcto XXX-XX-XXX-RELATIONSHIP
            $this->assertNotNull($result, "Should work for identifier: {$identifier}");

            $parts = explode('-', $identifier);
            array_pop($parts);
            $expectedBase = implode('-', $parts);

            $this->assertEquals($expectedBase, $result['base_identifier']);
        }
    }

    public function test_case_insensitive_relationship_matching(): void
    {
        $testCases = [
            '123-45-6789-spouse',     // lowercase
            '123-45-6789-Spouse',     // mixed case
            '123-45-6789-SPOUSE',     // uppercase
            '123-45-6789-child2',     // lowercase with number
            '123-45-6789-Child3',     // mixed case with number
        ];

        foreach ($testCases as $identifier) {
            $result = $this->callPrivateMethod('extractRelationshipFromIdentifier', [$identifier]);

            $this->assertNotNull($result, "Should work for identifier: {$identifier}");

            if (str_contains(strtoupper($identifier), 'SPOUSE')) {
                $this->assertEquals('SPOUSE', $result['relationship_code']);
            } elseif (str_contains(strtoupper($identifier), 'CHILD')) {
                $this->assertEquals('CHILD', $result['relationship_code']);
            }
        }
    }

    /**
     * Helper method to call private methods for testing
     */
    private function callPrivateMethod(string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass($this->command);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->command, $parameters);
    }
}
