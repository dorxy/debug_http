<?php
namespace DebugHttp\View\Helper;

use Cake\View\Helper;
use Cake\View\Helper\HtmlHelper;
use DOMDocument;

/**
 * Class CallHelper
 *
 * @property HtmlHelper Html
 *
 * @package DebugHttp\View\Helper
 */
class CallHelper extends Helper
{
    public $helpers = ['Html'];

    /**
     * Output request method
     *
     * @param string $method Method of a request, e.g. GET, POST, PUT
     *
     * @return string Span for method
     */
    public function method($method)
    {
        return $this->Html->tag('span', strtoupper($method), ['class' => 'method method-' . strtolower($method)]);
    }

    /**
     * Output response code
     *
     * @param int $code HTTP status code of a response
     *
     * @return string Span for code
     */
    public function code($code)
    {
        $codeRounded = ((int)($code / 100)) * 100;

        return $this->Html->tag('span', $code, ['class' => 'response-code code-' . $codeRounded]);
    }

    /**
     * Output call time
     *
     * @param float $time Duration of a request
     *
     * @return string Span with time in milliseconds
     */
    public function time($time)
    {
        return $this->Html->tag('span', $time * 1000 . ' ms', ['class' => 'time']);
    }

    /**
     * Formatted headers of a request/response
     *
     * @param array $headers Key value pairs of headers
     * @param string $name Title of the headers table
     *
     * @return string Table with headers
     */
    public function headers($headers, $name = null)
    {
        $html = '';
        if ($name) {
            $html = '<thead><tr><th colspan="2">' . $name . '</th></tr></thead><tbody>';
        }
        foreach ($headers as $key => $value) {
            if (is_array($value)) {
                $value = implode("\n\n", $value);
            }
            $html .= "<tr><td>$key</td><td>$value</td></tr>";
        }

        return '<table cellspacing="0" cellpadding="0" class="debug-table">' . $html . '</tbody></table>';
    }

    /**
     * Get formatted output body
     *
     * @param string $content Content of the body
     * @param string $type Type of the body, e.g. xml, json or html
     *
     * @return string
     */
    public function body($content, $type)
    {
        if (empty($content)) {
            return '<pre data-format-text><span style="color:darkgrey;">Empty body</span></pre>';
        }

        if (is_array($content)) {
            $content = http_build_query($content);
        }

        $contentType = 'text';
        foreach (['json', 'xml', 'html'] as $possibleType) {
            if (strpos(strtolower($type), $possibleType) !== false) {
                $contentType = $possibleType;
                break;
            }
        }
        $contentFormatted = '';
        switch ($contentType) {
            case 'json':
                $contentFormatted = json_encode(json_decode($content), JSON_PRETTY_PRINT);
                break;
            case 'xml':
                $doc = new DomDocument('1.0');
                $doc->preserveWhiteSpace = false;
                $doc->formatOutput = true;
                @$doc->loadXML($content); //our code is not responsible for badly formatted content
                $contentFormatted = $doc->saveXML();
                break;
            case 'html':
                $doc = new DomDocument('1.0');
                $doc->preserveWhiteSpace = false;
                $doc->formatOutput = true;
                @$doc->loadHTML($content); //our code is not responsible for badly formatted content
                $contentFormatted = $doc->saveHTML();
                break;
        }
        $copyButton = '<a href="javascript:;" class="select-response">Select</a>';
        $rawButton = '<a href="javascript:;" class="formatted" onclick="$(this).text($(this).text() == \'Raw\' ? \'Formatted\' : \'Raw\').next(\'pre\').find(\'> code\').toggle();">Raw</a>';


        $html = $copyButton;
        if (!empty($contentFormatted)) {
            $html .= $rawButton . '<pre>';
            $html .= $this->Html->tag('code', htmlentities($contentFormatted), ['class' => 'language-' . $contentType]);
            $html .= $this->Html->tag('code', htmlentities($content), ['class' => 'raw', 'style' => 'display:none;']);
        } else {
            $html .= '<pre>';
            $html .= $this->Html->tag('code', htmlentities($content), ['class' => 'raw']);
        }
        $html .= '</pre>';

        return $html;
    }

    /**
     * Get formatted stack trace
     *
     * @param string $trace Trace in string format
     *
     * @return string
     */
    public function stackTrace($trace)
    {
        $trace = str_replace("\n", '<br/>', $trace);

        return $this->Html->tag('pre', $trace);
    }
}
