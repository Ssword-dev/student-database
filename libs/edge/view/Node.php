<?php
namespace EdgeFramework\View;

class Node
{
    // the type of the node.
    public int $type;

    // the first child.
    public ?Node $child;

    // the next sibling.
    public ?Node $sibling;

    // the parent.
    public ?Node $return;

    // internal properties.
    private ?Node $_lastChild;

    // the constructor.
    public function __construct(int $type, $children = null)
    {
        $this->type = $type;

        // declare no siblings. (parent declares the actual siblings).
        $this->sibling = null;

        // declare no parent. (parent binds the children's return to itself).
        $this->return = null;

        // if there is no children. use null as child.
        if ($children === null || !isset($children[0])) {
            $this->child = null;
        }

        // if there *is* a children. go bind them to the current node.
        else {
            // bind the child property to the first child.
            $this->child = $children[0];

            // set the initial previous child to null. (no previous child.)
            $previousChild = null;

            // count the number of children to use in for loop.
            $childrenCount = count($children);

            for ($idx = 0; $idx < $childrenCount; $idx++) {
                // get the current child.
                $child = $children[$idx];

                // bind the return property of the child to this node.
                $child->return = $this;

                // there is a previous child (not the first child).
                // then bind the sibling property of the previous child to
                // the current child. this makes the graph of nodes all connected.
                if ($previousChild !== null) {
                    $previousChild->sibling = $child;
                }

                // now the current child is now the previous child.
                $previousChild = $child;
            }
        }
    }

    public function appendChild(Node $node)
    {
        if ($this->child === null) {
            $this->child = $node;
        } else {
            $current = $this->child;
            while ($current->sibling !== null) {
                $current = $current->sibling;
            }
            $current->sibling = $node;
        }
        $node->return = $this;
    }
}