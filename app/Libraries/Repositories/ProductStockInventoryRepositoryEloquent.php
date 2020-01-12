<?php

/** @noinspection ALL */

namespace App\Libraries\Repositories;

use App\Libraries\RepositoriesInterfaces\UsersRepository;
use App\Models\ProductStockInventory;
use App\Supports\BaseMainRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductStockInventoryRepositoryEloquent extends BaseRepository implements UsersRepository
{
    use BaseMainRepository;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductStockInventory::class;
    }

    /**
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param null $input
     * @return array
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getDetails($input = null)
    {
        $value = $this->makeModel();
        $this->commonFilterFn($value, $input);
        $count = $value->count();
        $this->getCommonPaginationFilterFn($value, $input);

        return [
            'count' => $count,
            'list' => $value,
        ];
    }

    /**
     * commonFilterFn => make common filter for list and getDetailsByInput
     *
     * @param mixed $value
     * @param mixed $input
     *
     * @return void
     */
    protected function commonFilterFn(&$value, $input)
    {
        /** searching */
        if (isset($input['search'])) {
            $value = $this->customSearch($value, $input, ['product_id', 'common_product_attribute_size_id', 'common_product_attribute_color_id']);
        }

        $this->customRelation($value, $input, []); //'account_detail'

        /** filter by id  */
        if (isset($input['id'])) {
            $value = $value->where('id', $input['id']);
        }

        if (isset($input['ids']) && is_array($input['ids']) && count($input['ids'])) {
            $value = $value->whereIn('id', $input['ids']);
        }

        /** product_id and product_ids wise filter */
        if (isset($input['product_id'])) {
            $value = $value->where('product_id', $input['product_id']);
        }
        if (isset($input['product_ids']) && count($input['product_ids']) > 0) {
            $value = $value->whereIn('product_id', $input['product_ids']);
        }

        /** common_product_attribute_size_id and common_product_attribute_size_ids wise filter */
        if (isset($input['common_product_attribute_size_id'])) {
            $value = $value->where('common_product_attribute_size_id', $input['common_product_attribute_size_id']);
        }
        if (isset($input['common_product_attribute_size_ids']) && count($input['common_product_attribute_size_ids']) > 0) {
            $value = $value->whereIn('common_product_attribute_size_id', $input['common_product_attribute_size_ids']);
        }

        /** common_product_attribute_color_id and common_product_attribute_color_ids wise filter */
        if (isset($input['common_product_attribute_color_id'])) {
            $value = $value->where('common_product_attribute_color_id', $input['common_product_attribute_color_id']);
        }
        if (isset($input['common_product_attribute_color_ids']) && count($input['common_product_attribute_color_ids']) > 0) {
            $value = $value->whereIn('common_product_attribute_color_id', $input['common_product_attribute_color_ids']);
        }

        if (isset($input['sale_price'])){
            return $value->where('sale_price', $input['sale_price']);
        }

        if (isset($input['mrp_price'])){
            return $value->where('mrp_price', $input['mrp_price']);
        }

        if (isset($input['stock_available'])){
            return $value->where('stock_available', $input['stock_available']);
        }

        /** date wise records */
        if (isset($input['start_date'])) {
            $value = $value->where('created_at', ">=", $input['start_date']);
        }
    }

    /**
     * getCommonPaginationFilterFn => get pagination and get data
     *
     * @param mixed $value
     * @param mixed $input
     *
     * @return void
     */
    protected function getCommonPaginationFilterFn(&$value, $input)
    {
        if (isset($input['list'])) {
            $value = $value->select($input['list']);
        }

        if (isset($input['page']) && isset($input['limit'])) {
            $value = $this->customPaginate($value, $input);
        }

        if (isset($input['sort_by']) && count($input['sort_by']) > 0) {
            $value = $value->orderBy($input['sort_by'][0], $input['sort_by'][1]);
        } else {
            $value = $value->ordered();
        }

        if (isset($input['first']) && $input['first'] == true) {
            $value = $value->first();
        } elseif (isset($input['is_deleted']) && $input['is_deleted'] == true) {
            $value = $value->withTrashed()->get();
        } else {
            $value = $value->get();
        }
    }

    /**
     * @param $input
     * @param $id
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateRich($input, $id)
    {
        $value = $this->makeModel();
        $value = $value->whereId($id)->first();

        // $value->fill($input)->update();
        if (isset($value)) {
            $value->fill($input)->update();
            return $value->fresh();
        }
    }

    /** get details by input
     * @param null $input
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getDetailsByInput($input = null)
    {
        $value = $this->makeModel();

        $this->commonFilterFn($value, $input);

        $this->getCommonPaginationFilterFn($value, $input);

        return $value;
    }

    /** Check key exists in db or not - RESPONSE BOOLEAN
     * @param $key
     * @param $input
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function checkKeysExist($key, $input)
    {
        $value = $this->makeModel();

        $value = $value->where($key, $input[$key]);
        if ($value->first()) {
            return true;
        } else {
            return false;
        }
    }

    /** get records by input
     * @param $input
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getRecords($input)
    {
        $value = $this->makeModel();

        if (isset($input['email'])) {
            $value = $value->whereEmail($input['email']);
        }

        if (isset($input['first'])) {
            $value = $value->first();
        } else {
            $value = $value->get();
        }
        return $value;
    }

    /** check for email is exists or not
     * @param null $input
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function checkEmailExists($input = null)
    {
        $value = $this->makeModel();
        $value = $value->whereEmail($input['email']);
        $value = $value->first();
        return $value;
    }

    /** check records for is deleted or not
     * @param null $input
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function checkEmailRecordDeleted($input = null)
    {
        $value = $this->makeModel();
        $value = $value->whereEmail($input['email']);
        $value = $value->withTrashed()->first();
        return $value;
    }

    /**
     * getUserCountByType => get user  count by their types
     *
     * @return void
     */
    public function getUserCountByType()
    {
        $value = $this->makeModel();

        return $value;
    }

    public function updateManyByWhere($input, $where)
    {
        $value = $this->makeModel();
        $value = $value->where(array_first(array_keys($where)), array_first(array_values($where)));
        // $value = $value->where('user_id', $where['user_id']);
        $value = $value->update($input);
        return $value;

        /** for return updated object */
        // $value->fill($input)->update();
        return $value->fresh();
    }

    public function deleteWhereIn($key, $array)
    {
        $value = $this->makeModel();
        return $value->whereIn($key, $array)->delete();
    }
}
