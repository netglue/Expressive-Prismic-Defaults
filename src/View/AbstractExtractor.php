<?php
declare(strict_types=1);

namespace ExpressivePrismic\View;
use Prismic;
use Prismic\Document;
use Prismic\Fragment\Group;
use Prismic\Fragment\GroupDoc;
use Prismic\Fragment\SliceZone;

abstract class AbstractExtractor
{

    /**
     * Return a plain text value for a document, group, group doc or slice zone
     *
     * It's unlikely that you'd ever use anything other than a Prismic\Document
     * but as a general purpose extractor it offers a bit more flexibility for
     * other use cases.
     *
     * @param Document|Group|GroupDoc|SliceZone $document
     * @param string $fragmentName
     * @return string|null
     */
    protected function getText($document, string $fragmentName)
    {
        $fragment = null;

        if ($document instanceof Document) {
            $fragment = $this->getTargetFragmentFromDocument($document, $fragmentName);
        }

        if ($document instanceof Group) {
            $fragment = $this->getTargetFragmentFromGroup($document, $fragmentName);
        }

        if ($document instanceof GroupDoc) {
            $fragment = $this->getTargetFragmentFromGroupDoc($document, $fragmentName);
        }

        if ($document instanceof SliceZone) {
            $fragment = $this->getTargetFragmentFromSliceZone($document, $fragmentName);
        }

        if ($fragment) {
            $data = $fragment->asText();
            return empty($data) ? null : $data;
        }

        return null;
    }

    /**
     * Try to locate the given fragment name in the given document
     *
     * @param Document $document
     * @param string $fragmentName
     * @return Prismic\Fragment\FragmentInterface|null
     */
    protected function getTargetFragmentFromDocument(Document $document, string $fragmentName)
    {
        $doctype  = $document->getType();
        $search   = strpos($fragmentName, '.') !== false
                  ? $fragmentName
                  : sprintf('%s.%s', $doctype, $fragmentName);
        return $document->get($search);
    }

    /**
     * Try to locate the given fragment name in any groups found within a slice zone
     *
     * @param SliceZone $zone
     * @param string $fragmentName
     * @return Prismic\Fragment\FragmentInterface|null
     */
    protected function getTargetFragmentFromSliceZone(SliceZone $zone, string $fragmentName)
    {
        foreach ($zone->getSlices() as $slice) {
            if ($slice->getValue() instanceof Group) {
                $fragment = $this->getTargetFragmentFromGroup($slice->getValue(), $fragmentName);
                if ($fragment) {
                    return $fragment;
                }
            }
        }

        return null;
    }

    /**
     * Try to locate the given fragment name in a group
     *
     * @param Group $group
     * @param string $fragmentName
     * @return Prismic\Fragment\FragmentInterface|null
     */
    protected function getTargetFragmentFromGroup(Group $group, string $fragmentName)
    {
        foreach ($group->getArray() as $groupDoc) {
            $fragment = $this->getTargetFragmentFromGroupDoc($groupDoc, $fragmentName);
            if ($fragment) {
                return $fragment;
            }
        }

        return null;
    }

    /**
     * Try to locate the given fragment name in the given groupdoc
     *
     * @param GroupDoc $group
     * @param string $fragmentName
     * @return Prismic\Fragment\FragmentInterface|null
     */
    protected function getTargetFragmentFromGroupDoc(GroupDoc $group, string $fragmentName)
    {
        $fragment = $group->get($fragmentName);
        if ($fragment) {
            return $fragment;
        }

        return null;
    }


}
