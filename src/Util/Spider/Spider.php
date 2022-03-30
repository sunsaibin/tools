<?php
namespace Sabine\Tools\Util\Spider;

require 'phpQuery.php';

class Spider
{
    /**
     * @param array $fields 字段数组
     *                  field=>字段名
     *                  selector=>选择器（任意的jQuery选择器语法）
     *                  type=>类型（说明：值 "text" ,"html" ,"HTML标签属性"）
     *                  fitter_list=>过滤列表
     *                  search=>被替换的子串
     *                  req=>被替换的子串正则表达式
     *                  replace=>要替换的子串
     * @param string $url
     * @return array
     */
    public function query(array $fields, $url='')
    {
        $data = [];
        $rules = [];
        foreach ($fields as $fieldsItem) {
            if (!isset($fieldsItem['field']) || !isset($fieldsItem['selector'])) {
                return $data;
            }
            $rules[$fieldsItem['field']] = [
                $fieldsItem['selector'],
                $fieldsItem['type'],
                $fieldsItem['fitter_list'] ?? '',
            ];
            if (isset($fieldsItem['str_replace'])) {
                $func = function ($str) use($fieldsItem) {
                    foreach ($fieldsItem['str_replace'] as $replaceItem) {
                        $replace = $replaceItem['replace'] ?? '';
                        if (isset($replaceItem['reg'])) {
                            $str =preg_replace($replaceItem['reg'],$replace,$str);
                        } else {
                            $str = str_replace($replaceItem['search'],$replace,$str);
                        }
                    }
                    return $str;
                };
                $rules[$fieldsItem['field']][] = $func;
            }
        }
        $html = file_get_contents($url);
        $data= \QueryList::Query($html,$rules)->getData();
        return $data;
    }
}
