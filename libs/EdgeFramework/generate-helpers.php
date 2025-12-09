<?php

require_once dirname(__DIR__) . '/view/index.php';

function isPhpKeyword(string $str): bool
{
    $phpKeywords = [
        '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable',
        'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare',
        'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare',
        'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval',
        'exit', 'extends', 'final', 'finally', 'for', 'foreach', 'function',
        'global', 'goto', 'if', 'implements', 'include', 'include_once',
        'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace',
        'new', 'or', 'print', 'private', 'protected', 'public', 'require',
        'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try',
        'unset', 'use', 'var', 'while', 'xor', 'yield', 'fn', 'match',
        // Also consider magic constants and type declarations as reserved in certain contexts
        '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__',
        '__METHOD__', '__NAMESPACE__', '__TRAIT__', 'string', 'int', 'float',
        'bool', 'array', 'object', 'iterable', 'void', 'null', 'self', 'parent', 'static'
    ];

    return in_array(strtolower($str), $phpKeywords);
}

function generateHelperScriptFile(){
    $script = "<?php\nnamespace EdgeFramework\\View\\IntrinsicElements;\nuse EdgeFramework\\View\\Element;\n";

    $spec = new DocumentSpec();

    foreach($spec->htmlTags as $htmlElement => $_){
        if (isPhpKeyword($htmlElement)) continue;
        $script .= "function $htmlElement(array \$attrs,...\$children){\n\treturn new Element('$htmlElement',\$attrs,\$children);\n}\n";
    }

    $scriptPath = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'view', 'intrinsic-elements', 'HTML.php']);

    $fp = fopen($scriptPath,'w');

    fwrite($fp, $script);
    fclose($fp);
}

generateHelperScriptFile();