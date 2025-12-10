<?php
namespace EdgeFramework\Routing\Enums;

enum OutputKind: int {
    case Buffered = 0;
    case Streamed = 1;
}
