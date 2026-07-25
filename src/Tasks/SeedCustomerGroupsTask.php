<?php

namespace Schrattenholz\OrderProfileFeature\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\PolyExecution\PolyOutput;
use SilverStripe\Security\Group;
use Schrattenholz\OrderProfileFeature\OrderCustomerGroup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Seeds the two customer-facing account types this shop is built around: "Privatkunde"
 * (retail customer, the default) and "Wiederverkäufer" (reseller, gets a confirmation
 * workflow and its own pricing tier). Each is a SilverStripe Security\Group plus a
 * Schrattenholz\OrderProfileFeature\OrderCustomerGroup record that carries the shop-specific
 * config (short code shown in the CMS pricing grids, VAT rate/note, whether it's selectable
 * during registration).
 *
 * OrderConfigID is deliberately left unset (0) -- linking it to the shop's OrderConfig
 * singleton, and setting the singleton's own content (emails, footer text, page links, logo,
 * ...) is inherently client-specific and has to be done once by hand via the CMS after
 * install, not something that can be meaningfully seeded as generic reference data.
 *
 * Idempotent: skips a group if one with the same Code already exists.
 */
class SeedCustomerGroupsTask extends BuildTask
{
    protected static string $commandName = 'seed-customer-groups';

    protected string $title = 'Seed customer groups (Privatkunde / Wiederverkäufer)';

    protected static string $description = 'Creates the Privatkunde and Wiederverkäufer '
        . 'Security groups plus their OrderCustomerGroup shop config. Skips groups that '
        . 'already exist (matched by Code).';

    private const GROUPS = [
        [
            'Code' => 'privatkunden',
            'Title' => 'Privatkunde',
            'ShortCode' => 'PK',
            'IsDefault' => true,
            'VatExluded' => false,
            'Vat' => 9.50,
            'VatNote' => 'Die angegebenen Preise verstehen sich inklusive einer MwSt. von 9,5%.',
            'SelectableForFrontendUser' => true,
        ],
        [
            'Code' => 'wiederverkaeufer',
            'Title' => 'Wiederverkäufer',
            'ShortCode' => 'WV',
            'IsDefault' => false,
            'VatExluded' => false,
            'Vat' => 9.50,
            'VatNote' => 'Die angegebenen Preise verstehen sich inklusive einer MwSt. von 9,5%.',
            'SelectableForFrontendUser' => true,
        ],
    ];

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        foreach (self::GROUPS as $data) {
            if (Group::get()->filter('Code', $data['Code'])->exists()) {
                $output->writeln("Group '{$data['Code']}' already exists -- skipping.");
                continue;
            }

            $group = Group::create();
            $group->Title = $data['Title'];
            $group->Code = $data['Code'];
            $group->write();

            $customerGroup = OrderCustomerGroup::create();
            $customerGroup->GroupID = $group->ID;
            $customerGroup->ShortCode = $data['ShortCode'];
            $customerGroup->IsDefault = $data['IsDefault'];
            $customerGroup->VatExluded = $data['VatExluded'];
            $customerGroup->Vat = $data['Vat'];
            $customerGroup->VatNote = $data['VatNote'];
            $customerGroup->SelectableForFrontendUser = $data['SelectableForFrontendUser'];
            $customerGroup->write();

            $output->writeln("Created group '{$data['Title']}' (Code: {$data['Code']}) with OrderCustomerGroup #{$customerGroup->ID}.");
        }

        $output->writeln('Note: OrderConfigID was left unset. Create/edit the shop\'s OrderConfig '
            . 'via the CMS and it can be linked to these groups afterwards if needed.');

        return Command::SUCCESS;
    }
}
