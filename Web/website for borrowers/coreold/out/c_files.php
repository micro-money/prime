<?php
/**
 * Created by PhpStorm.
 * User: SANEK333
 * Date: 10.10.2015
 * Time: 11:51
 */
namespace AlexKonov;

class Exception extends \Exception
{
}

//todo: удалить
$db['host'] = 'localhost';
$db['username'] = 'root';
$db['password'] = '';
$db['base'] = 'nalog';
require_once('f_mysql.php');


//фильтрация данных
class Esc
{

    static public function sql($value, $type = 'string')
    {
        if ($type == 'string')
            $value = self::html($value);
        elseif ($type == 'int')
            $value = intval($value);
        elseif ($type == 'float')
            $value = floatval($value);
        elseif ($type == 'bool')
            $value = (int)!!$value;
        elseif ($type == 'array')
            $value = json_encode(is_array($value) ? $value : [$value]);
        elseif ($type == 'timestamp') {
            if (preg_match('/^\d+$/s', $value))
                $value = date('Y-m-d H:i:s');
            elseif (!preg_match('/^\d{4}\-\d{2}\-\d{2}\ \d{2}\:\d{2}\:\d{2}$/s', $value))
                $value = date('Y:m:d H:i:s');
        } else
            $value = self::html($value);
        return mysql_real_escape_string($value);
    }

    static public function html($str)
    {
        return htmlspecialchars((string)$str);
    }
}

//сущность
class Entity
{
    private
        $_fields = null;
    protected
        $_table,
        $_types = [];
    public
        $id = 0,
        $user_id = 0,
        $created_at = '0000-00-00 00:00:00',
        $updated_at = '0000-00-00 00:00:00',
        $deleted_at = '0000-00-00 00:00:00';

    public function __construct()
    {
        $this->_setTypes();
        $this->_setFields();
    }

    //указываем типы данных для фильтрации
    protected function _setTypes()
    {
        $this->_types['id'] = 'int';
        $this->_types['user_id'] = 'int';
        $this->_types['created_at'] = 'timestamp';
        $this->_types['updated_at'] = 'timestamp';
        $this->_types['deleted_at'] = 'timestamp';
    }

    //загрузить запись в объект;
    //по умолчанию грузятся только не удаленные записи
    //если надо в том числе удаленные, то $all=true
    public function get($id, $all = false)
    {
        $result = db_row("SELECT * FROM `$this->_table` WHERE `id`=" . esc::sql($id, 'int') .
            (!$all ? (" and `deleted_at`='0000-00-00 00:00:00'") : ''));
        if ($result)
            return $this->setFieldsFromArray($result);
        return false;
    }

    //сохраняем запись в бд, если записи с id нет, создаётся, иначе редактируется
    //$types - типы данных для фильтрации
    public function save($types = null)
    {
        if (is_array($types)) {
            foreach ($this->types as $field => $type)
                if (!isset($types[$field]))
                    $types[$field] = $type;
        } else
            $types = $this->_types;
        $values = $this->__toArray();
        $values['id'] = abs(intval($values['id']));
        $data = [];
        $create = true;
        if ($values['id'] > 0) {
            if (db_result("SELECT COUNT(*) FROM `$this->_table` WHERE `id`=" . Esc::sql($values['id'], $this->_types['id'])) > 0)
                $create = false;
        }
        if ($create) {
            if ($values['id'] > 0)
                $data[] = "`id`=" . Esc::sql($values['id'], 'int');
            if ($values['created_at'] == '0000-00-00 00:00:00')
                $values['created_at'] = date('Y:m:d H:i:s');
        } else {
            if ($values['updated_at'] == '0000-00-00 00:00:00')
                $values['updated_at'] = date('Y:m:d H:i:s');
        }
        foreach ($values as $k => $v)
            if ($k != 'id')
                $data[] = "`$k` = '" . Esc::sql($v, (isset($types[$k]) ? $types[$k] : null)) . "'";
        if ($create) {
            $result = db_insert("INSERT INTO `$this->_table` SET " . implode(', ', $data));
            if (!$result)
                return false;
        } else {
            $result = db_request("UPDATE `$this->_table` SET " . implode(', ', $data) . " WHERE `id`=" . Esc::sql($values['id'], $this->_types['id']));
            if (!$result)
                return false;
            $result = $values['id'];
        }
        return $this->get($result);
    }

    //"мягко" удаляем запись
    public function remove()
    {
        if ($this->deleted_at == '0000-00-00 00:00:00') {
            $this->deleted_at = date('Y-m-d H:i:s');
            db_request("UPDATE `$this->_table` SET `deleted_at`='" . Esc::sql($this->deleted_at, $this->_types['deleted_at']) . "' WHERE `id`=" . Esc::sql($this->id, $this->_types['id']));
        }
        return $this;
    }

    //восстанавливаем запись
    public function restore()
    {
        if ($this->deleted_at != '0000-00-00 00:00:00') {
            $this->deleted_at = '0000-00-00 00:00:00';
            db_request("UPDATE `$this->_table` SET `deleted_at`='" . Esc::sql($this->deleted_at, $this->_types['deleted_at']) . "' WHERE `id`=" . Esc::sql($this->id, $this->_types['id']));
        }
        return $this;
    }

    public function isDeleted()
    {
        return $this->deleted_at=='0000-00-00 00:00:00';
    }

    //полное удаление записи
    public function delete()
    {
        db_request("DELETE FROM `$this->_table` WHERE `id`=" . Esc::sql($this->id, $this->_types['id']) . chr(10));
        $this->id = 0;
        return true;
    }

    //заполняем объект из массива
    public function setFieldsFromArray($data)
    {
        $fields = $this->__toArray();
        foreach ($fields as $k => $v)
            if (isset($data[$k])) {
                switch ($this->_types[$k]) {
                    case 'array':
                        if (!is_array($data[$k])) {
                            if (!$tmp = @json_decode($data[$k]))
                                $tmp = [$tmp];
                            $data[$k] = is_array($tmp) ? $tmp : [$tmp];
                        }
                        break;
                }
                $this->$k = $data[$k];
            }
        return $this;
    }

    public function __toArray()
    {
        $this->_setFields();
        $result = [];
        foreach ($this->_fields as $k => $v)
            $result[$k] = $this->$k;
        return $result;
    }

    public function __set($name, $value)
    {
        $fields = $this->__toArray();
        if (isset($fields[$name]))
            $this->$name = $value;
        elseif (!property_exists($this, $name)) {
            $this->$name = $value;
            $this->_setFields($name);
        }

    }

    public function __toString()
    {
        return json_encode($this->__toArray());
    }

    //определяем свойства класса, являющиеся полями сущности
    protected function _setFields($field = null)
    {
        if (!$this->_fields) {
            $this->_fields = [];
            preg_match_all('/\[([a-z_][a-z0-9_]*)\]/si', print_r($this, true), $list);
            $this->_fields = [];
            foreach ($list[1] as $v)
                $this->_fields[$v] = null;
        }
        if ($field)
            $this->_fields[$field] = null;
        return $this->_fields;
    }
}

//Список сущностей
class EntityList
{
    protected
        $_table,
        $_types = [],
        $_list = [];

    public function __construct()
    {
        $this->_setTypes();
    }

    //выбрать все записи
    public function all($sort = 'DESC', $all = false)
    {
        return $this->search([], [], $sort, $all);
    }

    //выбрать записи, удовлетворяющие условиям
    //$conditions - массив условий в формате билдера ($this->_builder)
    //$navigation - массив для ограничения по количеству ['start'=>'нач. значение', 'limit'=>'кол-во записей']
    public function search($conditions=[], $navigation=[], $sort = 'DESC', $all = false)
    {
        if (!$all)
            $conditions['deleted_at'] = '0000-00-00 00:00:00';
        $conditions = $this->_builder($conditions);
        if (!is_array($sort) || !isset($sort['field'], $sort['type']))
            $sort = mb_strtoupper($sort) == 'DESC' ? '`id` DESC' : '`id` ASC';
        else {
            if (!isset($this->_types[$sort['field']]))
                $sort['field'] = 'id';
            $sort = mb_strtoupper($sort) == 'DESC' ? '`' . $sort['field'] . '` DESC' : '`' . $sort['field'] . '` ASC';
        }
        if (isset($navigation['start'], $navigation['limit']))
            $limit = " LIMIT " . intval($navigation['start']) . ", " . intval($navigation['limit']);
        else
            $limit = '';
        $result = db_array("SELECT * FROM `$this->_table`" .
            (strlen($conditions) ? " WHERE $conditions" : '') .
            "ORDER by $sort" . $limit);
        $this->_list = $result;
        return $this->_list;
    }

    //подсчитать записи, удовлетворяющие условиям
    public function count($conditions, $all = false)
    {
        if (!$all)
            $conditions['deleted_at'] = '0000-00-00 00:00:00';
        $conditions = $this->_builder($conditions);
        return db_result("SELECT COUNT(*) FROM `$this->_table`" . (strlen($conditions) ? " WHERE $conditions" : ''));
    }

//указываем типы данных для фильтрации
    protected function _setTypes()
    {
        $this->_types['id'] = 'int';
        $this->_types['user_id'] = 'int';
        $this->_types['created_at'] = 'timestamp';
        $this->_types['updated_at'] = 'timestamp';
        $this->_types['deleted_at'] = 'timestamp';
    }

    //конструктор запросов
    // для условия задаём оператор перед именем поля, например ['!field'='value']
    // >, < : больше, меньше (для int, float, bool, timestamp)
    // >=, <= : больше или равно, меньше или раньше (для int, float, bool, timestamp)
    // !, = : не равно, равно (для всех, кроме array)
    // % = LIKE (для string)
    // * = IN (для всех, кроме array)
    //
    //todo: сделать поиск по типу array
    private function _builder($conditions)
    {
        $result = [];
        foreach ($conditions as $k => $condition) {
            $k = $this->_expression($k);
            if ($k[1] == 'and' and is_array($condition)) {
                $tmp = [];
                foreach ($condition as $k2 => $v) {
                    $k2 = $this->_expression($k2);
                    if ($k2[1] == 'and' || $k2[1] == 'or' || isset($this->_types[$k2[1]])) {
                        $str = '';
                        if (is_array($v) || isset($this->_types[$k2[1]]))
                            $str = $this->_builder([$k2[0] . $k2[1] => $v]);
                        if ($str and strlen($str))
                            $tmp[] = $str;
                    }
                }
                $count = count($tmp);
                if ($count == 1)
                    $result[] = $tmp[0];
                elseif ($count > 1)
                    $result[] = '(' . implode(' and ', $tmp) . ')';
            } elseif ($k[1] == 'or' and is_array($condition)) {
                $tmp = [];
                foreach ($condition as $k2 => $v) {
                    $k2 = $this->_expression($k2);
                    if ($k2[1] == 'and' || $k2[1] == 'or' || isset($this->_types[$k2[1]])) {
                        $str = '';
                        if (is_array($v) || isset($this->_types[$k2[1]]))
                            $str = $this->_builder([$k2[0] . $k2[1] => $v]);
                        if ($str and strlen($str))
                            $tmp[] = $str;
                    }
                }
                $count = count($tmp);
                if ($count == 1)
                    $result[] = $tmp[0];
                elseif ($count > 1)
                    $result[] = '(' . implode(' or ', $tmp) . ')';

            } elseif (isset($this->_types[$k[1]])) {
                $tmp = '';
                $t = $this->_types[$k[1]];
                switch ($t) {
                    case 'string':
                        if ($k[0] == '%')
                            $tmp = "`$k[1]` LIKE '" . Esc::sql($condition, $t) . "'";
                        elseif ($k[0] == '!')
                            $tmp = "`$k[1]` != '" . Esc::sql($condition, $t) . "'";
                        elseif ($k[0] == '*' and is_array($condition)) {
                            foreach ($condition as $k2 => $v2)
                                $condition[$k2] = Esc::sql($v2, $t);
                            $tmp = "`$k[1]` IN ('" . implode("', '", $condition) . "')";
                        } else
                            $tmp = "`$k[1]` = '" . Esc::sql($condition, $t) . "'";
                        break;
                    case 'int':
                    case 'float':
                    case 'bool':
                        if ($k[0] == '!')
                            $tmp = "`$k[1]` != " . Esc::sql($condition, $t);
                        elseif ($k[0] == '>' || $k[0] == '<' || $k[0] == '<=' || $k[0] == '>=')
                            $tmp = "`$k[1]` $k[0] " . Esc::sql($condition, $t);
                        elseif ($k[0] == '*' and is_array($condition)) {
                            foreach ($condition as $k2 => $v2)
                                $condition[$k2] = Esc::sql($v2, $t);
                            $tmp = "`$k[1]` IN (" . implode(", ", $condition) . ")";
                        } else
                            $tmp = "`$k[1]` = " . Esc::sql($condition, $t);
                        break;
                    case 'timestamp':
                        if ($k[0] == '!')
                            $tmp = "`$k[1]` != '" . Esc::sql($condition, $t) . "'";
                        elseif ($k[0] == '>' || $k[0] == '<')
                            $tmp = "`$k[1]` $k[0] '" . Esc::sql($condition, $t) . "'";
                        elseif ($k[0] == '*' and is_array($condition)) {
                            foreach ($condition as $k2 => $v2)
                                $condition[$k2] = Esc::sql($v2, $t);
                            $tmp = "`$k[1]` IN ('" . implode("', '", $condition) . "')";
                        } else
                            $tmp = "`$k[1]` = '" . Esc::sql($condition, $t) . "'";
                        break;
                }
                if (strlen($tmp))
                    $result[] = $tmp;
            }
        }
        return implode(' and ', $result);
    }

    private function _expression($expression)
    {
        $expression = mb_strtolower($expression);
        if (preg_match('/^([<>=!\*%]{1,2})[^a-z0-9_]*([a-z0-9_]+)[^a-z0-9_]*$/s', $expression, $result))
            return [$result[1], $result[2]];
        else
            return ['=', $expression];
    }

    public function __toArray()
    {
        return $this->_list;
    }

    public function __toString()
    {
        return json_encode($this->__toArray());
    }

}

//файл
class File extends Entity
{
    private
        $_root,
        $_errors = [];
    protected
        $_table = 'files';
    public
        $target = '',
        $type = '',
        $name = '',
        $description = '',
        $size = '',
        $path = false,
        $data = [];

    public function __construct($root, $id = 0)
    {
        parent::__construct();
        $this->_root = $root;
        if ($id > 0)
            $this->get($id);
    }

    protected function _setTypes()
    {
        parent::_setTypes();
        $this->_types['target'] = 'string';
        $this->_types['type'] = 'string';
        $this->_types['name'] = 'string';
        $this->_types['description'] = 'string';
        $this->_types['path'] = 'string';
        $this->_types['size'] = 'int';
        $this->_types['data'] = 'array';
    }

    public function save()
    {
        if (!$this->fileExists())
            return false;
        if (!$this->checkFileType())
            return false;
        return parent::save();
    }

    public function delete()
    {
        $this->path((string)$this->path);
        if ($this->fileExists())
            if (!@unlink($this->_root . '/' . $this->path)) {
                $this->_errors[] = 'Access denied. File "' . $this->path . '" is not deleted.';
                return false;
            }
        return parent::delete();
    }

    public function checkFileType()
    {
        if ($this->fileExists()) {
            $ext = mb_strtolower(preg_replace('/.+\.([a-z0-9]+)$/si', '\1', $this->path));
            switch ($ext) {
                default:
                    $this->_errors[] = 'Unknown file type (file: "' . $this->path . '").';
                    return false;
                    break;
                case 'jpeg':
                case 'jpg':
                case 'gif':
                case 'png':
                case 'bmp':
                    $this->type = 'image';
                    break;

                case 'doc':
                case 'docx':
                    $this->type = 'word';
                    break;

                case 'xls':
                case 'xlsx':
                    $this->type = 'excel';
                    break;

                case 'zip':
                case 'rar':
                case 'tar':
                case 'gz':
                case '7z':
                    $this->type = 'archive';
                    break;

                case 'ptt':
                case 'pptx':
                    $this->type = 'presentation';
                    break;

                case 'pdf':
                    $this->type = 'pdf';
                    break;
            }
            return true;
        }
        return false;
    }

    public function path($path)
    {
        $path = preg_replace('/\.{1,}/', '.', $path);
        $path = preg_replace('/\\{1,}/', '/', $path);
        $path = preg_replace('/\\{1,}/', '\\', $path);
        return $path;
    }

    public function fileExists()
    {
        $this->path = $this->path((string)$this->path);
        if (!strlen($this->path)) {
            $this->_errors[] = 'Path is empty.';
            return false;
        }
        if (!file_exists($this->_root . '/' . $this->path)) {
            $this->_errors[] = 'File "' . $this->path . '" not found.';
            return false;
        }
        if (!is_file($this->_root . '/' . $this->path)) {
            $this->_errors[] = 'Path "' . $this->path . '" is not file.';
            return false;
        }
        $this->size = filesize($this->_root . '/' . $this->path);
        return true;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

}


//Версия файла
class RepositoryFile extends Entity
{
    private
        $_root,
        $_errors = [];
    protected
        $_table = 'repository_files',
        $_file = null,
        $_repository;
    public
        $repository_id = 0,
        $file_id = 0;

    public function __construct($root, $id = 0)
    {
        parent::__construct();
        $this->_root = $root;
        if ($id > 0)
            $this->get($id);
    }

    protected function _setTypes()
    {
        parent::_setTypes();
        $this->_types['repository_id'] = 'int';
        $this->_types['file_id'] = 'int';
    }

    //greed - "жадность", вместе с версией сразу грузим прикреплённый файл
    public function get($id, $all = false, $greed = false)
    {
        $result = parent::get($id, $all);
        if ($result && $greed)
            $this->setFile(0);
        return $result;
    }

    public function getFile()
    {
        if ($this->file_id > 0) {
            if (!$this->_file || $this->file_id != $this->_file['id'])
                $this->_file = new File($this->file_id);
            return $this->_file;
        }
        return null;
    }

    public function getRepository()
    {
        if ($this->repository_id > 0) {
            if (!$this->_repository || $this->repository_id != $this->_repository['id'])
                $this->_repository = new Repository($this->repository_id);
            return $this->_repository;
        }
        return null;
    }

    //прикрепляем файл
    public function setFile($file = 0)
    {
        if (!is_object($file)) {
            $file = intval($file);
            if ($file > 0) {
                if ($file == $this->file_id && $this->_file && $this->_file->id == $file)
                    $file = $this->_file;
                else
                    $file = new File($this->_root, $file);
            } elseif ($this->file_id > 0) {
                if ($this->_file && $this->file_id == $this->_file->id)
                    $file = $this->_file;
                else
                    $file = new File($this->_root, $this->file_id);
            } else {
                $this->errors[] = 'Undefined file.';
                return false;
            }
        }
        if (!($file instanceof File))
            throw new Exception('Incorrect object!');
        if ($file->id == 0) {
            $this->errors[] = 'Empty file.';
            return false;
        }
        $this->file_id = $file->id;
        $this->_file = $file;
        return true;
    }

    //прикрепляем Репозиторий
    public function setRepository($repository = 0)
    {
        if (!is_object($repository)) {
            $repository = intval($repository);
            if ($repository > 0) {
                if ($repository == $this->repository_id && $this->_repository && $this->_repository->id == $repository)
                    $repository = $this->_repository;
                else
                    $repository = new Repository($this->_root, $repository);
            } elseif ($this->repository_id > 0) {
                if ($this->_repository && $this->repository_id == $this->_repository->id)
                    $repository = $this->_repository;
                else
                    $repository = new Repository($this->_root, $this->repository_id);
            } else {
                $this->errors[] = 'Undefined repository.';
                return false;
            }

        }
        if (!($repository instanceof Repository))
            throw new Exception('Incorrect object!');
        if ($repository->id == 0) {
            $this->errors[] = 'Empty repository.';
            return false;
        }
        $this->repository_id = $repository->id;
        $this->_repository = $repository;
        return true;
    }

    public function save()
    {
        if (!$this->setFile())
            return false;
        if (!$this->setRepository())
            return false;
        return parent::save();
    }

    public function remove()
    {
        if ($this->setFile(0)) {
            if (!$this->_file->remove()) {
                $this->_errors = array_merge($this->_errors, $this->_file->getErrors());
                return false;
            }
        }
        return parent::remove();
    }

    public function delete()
    {
        if ($this->setFile(0))
            if (!$this->_file->delete()) {
                $this->_errors = array_merge($this->_errors, $this->_file->getErrors());
                return false;
            }
        return parent::delete();
    }

    public function getErrors()
    {
        return $this->_errors;
    }

}

//Репозиторий (сущность для хранения множества версий объекта (например файлы))
//todo: нужно написать листинг версий, создание файла с учетом версии
//todo: написать еще два класса, один для листинга файлов (на случай работы вне репозитория)
//todo: второй: листинг репозиториев (версий) для навигации по хранилищу
//todo: еще раз продумать архитектуру, продумать хранение
class Repository extends Entity
{
    private
        $_root,
        $_errors = [],
        $_last_id;
    protected
        $_table = 'repositories',
        $_list;
    public
        $target = '',
        $name = '',
        $description = '',
        $data = [];

    public function __construct($root, $id = 0)
    {
        parent::__construct();
        $this->_root = $root;
        if ($id > 0)
            $this->get($id);
    }

    //greed - "жадность", вместе с репозиторием сразу грузим имеющиеся версии
    public function get($id, $all = false, $greed = false)
    {
        parent::get($id, $all);
        $result = parent::get($id, $all);
        if ($result) {
            $this->_last_id = $this->id;
            if ($greed)
                $this->getFiles(true);
            else
                $this->_list = null;
        }
        return $result;

    }

    protected function _setTypes()
    {
        parent::_setTypes();
        $this->_types['target'] = 'string';
        $this->_types['name'] = 'string';
        $this->_types['description'] = 'string';
        $this->_types['data'] = 'array';
    }

    //добавляем версию файла в репозиторий
    public function addFile($file)
    {
        if (!$this->_last_id || $this->id == !$this->_last_id) {
            $this->_errors[] = 'Repository is not saved.';
            return false;
        }
        if (!is_object($file)) {
            $file = intval($file);
            if ($file > 0)
                $file = new File($this->_root, $file);
            else {
                $this->errors[] = 'Undefined file.';
                return false;
            }
        }
        if (!($file instanceof File))
            throw new Exception('Incorrect object!');
        if ($file->id == 0) {
            $this->errors[] = 'Empty file.';
            return false;
        }
        $rFile = new RepositoryFile($this->_root);
        if (!$rFile->setRepository($this) || !$rFile->setFile($file) || !$rFile->save()) {
            $this->_errors = array_merge($this->_errors, $rFile->getErrors());
            return false;
        }
        if ($this->_list)
            $this->_list[] = $rFile;
        return true;
    }

    //возвращает актуальную версию файла
    public function getFile()
    {
        $data = db_row("SELECT * FROM `repository_files` WHERE `repository_id`=" . Esc::sql($this->id, $this->types['id']) . " ORDER by `created_at` DESC LIMIT 0, 1");
        if (!$data)
            return false;
        $file = new RepositoryFile($this->_root);
        $file->setFieldsFromArray($data);
        return $file;
    }

    //список объектов версий файлов
    //$refresh = true: принудительно дёргаем из БД
    public function getFiles($refresh = false, $sort = 'DESC')
    {
        if (!$refresh && $this->_list)
            return $this->_list;
        $sort = mb_strtoupper($sort) == 'DESC' ? 'DESC' : 'ASC';
        $list = db_array("SELECT * FROM `repository_files` WHERE `repository_id`=" . Esc::sql($this->id, $this->types['id']) . " ORDER by `created_at` $sort");
        $this->_list = [];
        foreach ($list as $k => $value) {
            $this->_list[$k] = new RepositoryFile($this->_root);
            $this->_list[$k]->setFieldsFromArray($value);
        }
        return $this->_list;
    }

    public function remove()
    {
        $this->getFiles(true);
        foreach ($this->_list as $file)
            if (!$file->remove()) {
                $this->_errors = array_merge($this->_errors, $file->getErrors());
                return false;
            }
        return parent::remove();
    }

    public function delete()
    {
        $this->getFiles(true);
        foreach ($this->_list as $file)
            if (!$file->delete()) {
                $this->_errors = array_merge($this->_errors, $file->getErrors());
                return false;
            }
        return parent::delete();
    }


    public function getErrors()
    {
        return $this->_errors;
    }

}

//коллекция репозитариев
class Repositories extends EntityList
{
    private
        $_root;
    protected
        $_table = 'repositories';


    public function __construct($root)
    {
        parent::__construct();
        $this->_root = $root;
    }

    public function all($sort = 'DESC', $all = false)
    {
        parent::all($sort, $all);
        $this->_toRepository();
        return $this->_list;
    }

    private function _toRepository()
    {
        foreach ($this->_list as $k => $v)
            if (is_array($v)) {
                $this->_list[$k]=new Repository($this->_root);
                $this->_list[$k]->setFieldsFromArray($v);
            }
    }

    public function search($conditions=[], $navigation=[], $sort = 'DESC', $all = false)
    {
        parent::search($conditions, $navigation, $sort, $all);
        $this->_toRepository();
        return $this->_list;
    }

    protected function _setTypes()
    {
        parent::_setTypes();
        $this->_types['target'] = 'string';
        $this->_types['name'] = 'string';
        $this->_types['description'] = 'string';
        $this->_types['data'] = 'array';
    }

}

//коллекция файлов
class Files extends EntityList
{
    private
        $_root;
    protected
        $_table = 'files';


    public function __construct($root)
    {
        parent::__construct();
        $this->_root = $root;
    }

    public function all($sort = 'DESC', $all = false)
    {
        parent::all($sort, $all);
        $this->_toFile();
        return $this->_list;
    }

    private function _toFile()
    {
        foreach ($this->_list as $k => $v)
            if (is_array($v)) {
                $this->_list[$k]=new File($this->_root);
                $this->_list[$k]->setFieldsFromArray($v);
            }
    }

    public function search($conditions=[], $navigation=[], $sort = 'DESC', $all = false)
    {
        parent::search($conditions, $navigation, $sort, $all);
        $this->_toFile();
        return $this->_list;
    }

    protected function _setTypes()
    {
        parent::_setTypes();
        $this->_types['target'] = 'string';
        $this->_types['type'] = 'string';
        $this->_types['name'] = 'string';
        $this->_types['description'] = 'string';
        $this->_types['path'] = 'string';
        $this->_types['size'] = 'int';
        $this->_types['data'] = 'array';
    }

}
