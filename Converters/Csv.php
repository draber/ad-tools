<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */


namespace AdTools\Converters;

use AdTools\Converters\ConverterInterface;
use Illuminate\Support\Facades\Log;

/**
 * Class Csv
 *
 * @package AdTools\Converters
 */
class Csv implements ConverterInterface
{

    private $dataArray = [];

    private $csv = '';

    private $config;

    private $enclosures = ["'", '"'];

    /**
     * @var array
     */
    private $delimiters = ["\t", ',', ';', ':','|'];

    private $escapes = ["\\", '"'];

    /**
     * Convert CSV to array
     *
     * @param string $csv
     * @param string [$config[delimiter]]
     * @param string [$config[enclosure]]
     * @param string [$config[escape]]
     */
    public function __construct($csv, array $config = [])
    {
        $this->csv    = $csv;
        $this->config = array_merge(
            [
                'delimiter' => $this->guessDelimiter(),
            ],
            $config
        );
        if (empty($this->config['escape'])) {
            $this->config['enclosure'] = $this->guessEnclosure();
        }
        if (empty($this->config['escape'])) {
            $this->config['escape'] = $this->guessEscape();
        }
    }

    /**
     * Detect delimiter of a CSV string
     *
     * @return string
     */
    public function guessDelimiter()
    {
        $counts = [];
        foreach ($this->delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($this->csv, $delimiter);
        }
        $winner = max($counts);
        return $winner > 0 && $winner >= substr_count($this->csv, "\n")
            ? array_search($winner, $counts)
            : '';
    }

    /**
     * Detect enclosure of the values in a CSV string
     *
     * @return string
     */
    public function guessEnclosure()
    {
        $regExp = sprintf('~(%s)%s~', implode('|', $this->enclosures), $this->config['delimiter']);
        preg_match($regExp, $this->csv, $matches);
        return !empty($matches[1]) ? $matches[1] : '';
    }

    /**
     * Detect escape character of the values in a CSV string
     *
     * @return string
     */
    public function guessEscape()
    {
        $escapes = [];
        foreach ($this->escapes as $escape) {
            $escapes[] = $this->encodeMarker($escape);
        }
        $regExp = sprintf('~(%s)%s~', implode('|', $escapes), $this->config['enclosure']);
        preg_match($regExp, $this->csv, $matches);
        return !empty($matches[1]) ? $matches[1] : '';
    }


    /**
     * @return array
     */
    public function getData()
    {
        if ($this->dataArray) {
            return $this->dataArray;
        }
        $this->dataArray = [];
        $rows            = explode("\n", $this->csv);
        foreach ($rows as $row) {
            $this->dataArray[] = array_map(
                [$this, 'removeTrailingWhiteSpace'],
                str_getcsv(
                    $row,
                    $this->encodeMarker($this->config['delimiter']),
                    $this->encodeMarker($this->config['enclosure']),
                    $this->encodeMarker($this->config['escape'])
                )
            );
        }
        return $this->dataArray;
    }


    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getDelimiters()
    {
        return $this->delimiters;
    }

    /**
     * @return array
     */
    public function getEnclosures()
    {
        return $this->enclosures;
    }

    /**
     * @return array
     */
    public function getEscapes()
    {
        return $this->escapes;
    }


    /**
     * Some CSV fields can have several entries have a \xc2\xa0 at the end
     */
    private function removeTrailingWhiteSpace($str)
    {
        $str = trim($str);
        return preg_replace('~\xc2\xa0$~', '', $str);
    }

    /**
     * @param $marker
     * @return string
     */
    private function encodeMarker($marker)
    {
        if (false !== strpos($marker, '\\')) {
            $marker = addslashes($marker);
        }
        return $marker;
    }
}
