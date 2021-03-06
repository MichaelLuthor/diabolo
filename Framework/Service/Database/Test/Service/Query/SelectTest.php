<?php
namespace X\Service\Database\Test\Service\Query;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Query\Select;
use X\Service\Database\Database;
use X\Service\Database\Query\Expression;
use X\Service\Database\Query\Condition;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class SelectTest extends TestCase {
    /**  */
    use DatabaseServiceTestTrait;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->cleanAllDatabase();
    }
    
    /**
     * @param unknown $dbName
     */
    private function doTestSelect( $dbName ) {
        # select all
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $rowCount = Query::select($dbName)->from('users')->all()->count();
        $this->assertEquals($insertCount, $rowCount);
        $this->dropTestTableUser($dbName);
        
        # select spefied columns
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $row = Query::select($dbName)->from('users')->column('name')->column('age','ageAlias')->one();
        $this->assertArrayHasKey('name', $row);
        $this->assertArrayHasKey('ageAlias', $row);
        $this->dropTestTableUser($dbName);
        
        # select expression
        $row = Query::select($dbName)->expression(new Expression('1+1'), 'CustomExpr')->one();
        $this->assertEquals(2, $row['CustomExpr']);
        
        # subquery
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $row = Query::select($dbName)->from('users')
            ->expression(Query::select($dbName)->expression(new Expression('1+1')), 'CusCol')
            ->where(['id'=>Query::select($dbName)->column('id')->from('users')->where(['group'=>'DEMO'])])
            ->one();
        $this->assertEquals(2, $row['CusCol']);
        $this->dropTestTableUser($dbName);
        
        # condition
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $rowCount = Query::select($dbName)->from('users')->where(['name'=>'U001-DM'])->all()->count();
        $this->assertEquals(1, $rowCount);
        $this->dropTestTableUser($dbName);
        
        # group and having
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $rowCount = Query::select($dbName)->from('users')
            ->column('group')
            ->expression(Expression::count(),'memCount')
            ->groupBy('group')->having(Condition::build()->greaterThan(Expression::count(), 1))
            ->all()
            ->count();
        $this->assertEquals(2, $rowCount);
        $this->dropTestTableUser($dbName);
        
        # order
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $row = Query::select($dbName)->column('id')->from('users')->orderBy('id', SORT_DESC)->one();
        $this->assertEquals(6, $row['id']);
        $this->dropTestTableUser($dbName);
        
        # limit and offset
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $row = Query::select($dbName)->column('id')->from('users')->orderBy('id', SORT_DESC)->limit(1)->offset(1)->one();
        $this->assertEquals(5, $row['id']);
        $this->dropTestTableUser($dbName);
        
        # join
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $joinCondition = Condition::build()->is(
            Expression::column('U1.id', $this->getDatabase($dbName)), 
            Expression::column('U2.id', $this->getDatabase($dbName))
        );
        $rowCount = Query::select($dbName)
            ->from('users', 'U1')
            ->join(Select::LEFT_JOIN, 'users', $joinCondition, 'U2')
            ->all()
            ->count();
        $this->assertEquals($insertCount, $rowCount);
        $this->dropTestTableUser($dbName);
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestSelect(TEST_DB_NAME_MYSQL);
    }
    
    /** */
    public function test_sqlite() {
        $this->checkTestable(TEST_DB_NAME_SQLITE);
        $this->doTestSelect(TEST_DB_NAME_SQLITE);
    }
    
    /** */
    public function test_postgresql() {
        $this->checkTestable(TEST_DB_NAME_POSTGRESQL);
        $this->doTestSelect(TEST_DB_NAME_POSTGRESQL);
    }
    
    /** */
    public function test_oracle() {
        $this->checkTestable(TEST_DB_NAME_ORACLE);
        $this->doTestSelect(TEST_DB_NAME_ORACLE);
    }
    
    /** */
    public function test_mssql() {
        $this->checkTestable(TEST_DB_NAME_MSSQL);
        $this->doTestSelect(TEST_DB_NAME_MSSQL);
    }
    
    /** */
    public function test_firebird() {
        $this->checkTestable(TEST_DB_NAME_FIREBIRD);
        $this->doTestSelect(TEST_DB_NAME_FIREBIRD);
    }
}