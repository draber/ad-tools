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

namespace AdTools\Normalizers;

use AdTools\Exceptions\UnsupportedLanguageException;

/**
 * Class Stopwords
 *
 * @package AdTools\Normalizers
 */
class Stopwords
{

    private $stopwordList = [];

    private $text = '';

    /**
     * Stopwords constructor.
     *
     * @param $locale
     * @throws UnsupportedLanguageException
     */
    public function __construct($locale)
    {
        $list = dirname(__DIR__) . sprintf('/resources/stopwords/%s.txt', strtolower(substr($locale, 0, 2)));
        if (!is_file($list)) {
            throw new UnsupportedLanguageException(sprintf('No stopword list available for locale "%s"', $locale));
        }
        $this->stopwordList = array_filter(array_map('trim', file($list)));
    }

    /**
     * Retrieve stopword list
     * @return array
     */
    public function getList() {
        return $this->stopwordList;
    }

    /**
     * Remove stopwords from a text
     *
     * @return mixed
     */
    public function remove($text)
    {
        $stopwordRegExp = implode('|', $this->stopwordList);
        return preg_replace_callback("~(\W)(" . $stopwordRegExp . ")(\W)~iu", function($matches) {
            return str_replace('  ', ' ', $matches[1] . $matches[3]);
        }, $text);
    }
}
