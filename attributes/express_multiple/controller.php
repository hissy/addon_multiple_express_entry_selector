<?php

namespace Concrete\Package\MultipleExpressEntrySelector\Attribute\ExpressMultiple;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;

class Controller extends \Concrete\Attribute\Express\Controller
{
    public function form()
    {
        $entryOptions = [];

        /** @var Entity $entity */
        $entity = $this->getEntity();
        $entries = $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Entry')
            ->findBy(['entity' => $entity->getId()]);

        /** @var Entry $entry */
        foreach ($entries as $entry) {
            $entryOptions[] = ['id' => $entry->getID(), 'text' => $entry->getLabel()];
        }

        $selectedEntryIDs = [];
        if ($this->attributeValue) {
            $value = $this->attributeValue->getValueObject();
            if (is_object($value)) {
                $selectedEntries = $value->getSelectedEntries();
                foreach ($selectedEntries as $entry) {
                    $selectedEntryIDs[] = $entry->getID();
                }
            }
        }
        $this->set('entries', $entries);
        $this->set('entryOptions', $entryOptions);
        $this->set('selectedEntryIDs', $selectedEntryIDs);
        $this->set('entity', $entity);
        $this->requireAsset('selectize');
    }

    public function createAttributeValueFromRequest()
    {
        $entries = [];
        $data = $this->post();
        if (isset($data['value'])) {
            foreach (explode(',', $data['value']) as $datum) {
                $entries[] = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry')
                    ->findOneById($datum);
            }
        }

        return $this->createAttributeValue($entries);
    }
}