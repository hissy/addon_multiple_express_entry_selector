<?php

namespace Concrete\Package\MultipleExpressEntrySelector\Attribute\ExpressMultiple;

use Concrete\Core\Entity\Attribute\Value\Value\ExpressValue;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;

class Controller extends \Concrete\Attribute\Express\Controller
{
    protected $searchIndexFieldDefinition = ['type' => 'text', 'options' => ['default' => null, 'notnull' => false]];

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

    public function getSearchIndexValue()
    {
        $str = "||";
        $o = $this->attributeValue;
        if (is_object($o)) {
            $entries = $o->getValue()->getSelectedEntries();
            if (count($entries) > 0) {
                foreach ($entries as $entry) {
                    $str .= $entry->getLabel() . "||";
                }
            }
        }
        // remove line break for empty list
        if ($str == "||") {
            return '';
        }

        return $str;
    }

    public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    {
        if ($value instanceof ExpressValue) {
            /** @var \Doctrine\DBAL\Query\QueryBuilder $qb */
            $qb = $list->getQueryObject();
            $i = 1;
            $expressions = [];
            foreach ($value->getSelectedEntries() as $entry) {
                $column = 'ak_' . $this->attributeKey->getAttributeKeyHandle();
                $expressions[] = $qb->expr()->like($column, ':entryLabel' . $i);
                $qb->setParameter('entryLabel' . $i, "%||" . $entry->getLabel() . '||%');
                ++$i;
            }
            $expr = $qb->expr();
            $qb->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        }
    }
}