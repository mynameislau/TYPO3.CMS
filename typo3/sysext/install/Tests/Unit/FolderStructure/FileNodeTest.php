<?php
namespace TYPO3\CMS\Install\Tests\Unit\FolderStructure;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Install\FolderStructure\Exception;
use TYPO3\CMS\Install\FolderStructure\Exception\InvalidArgumentException;
use TYPO3\CMS\Install\FolderStructure\FileNode;
use TYPO3\CMS\Install\Tests\Unit\FolderStructureTestCase;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;

/**
 * Test case
 */
class FileNodeTest extends FolderStructureTestCase
{
    /**
     * Subject is not notice free, disable E_NOTICES
     */
    protected static $suppressNotices = true;

    /**
     * @test
     */
    public function constructorThrowsExceptionIfParentIsNull()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1366927513);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $node->__construct([], null);
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionIfNameContainsForwardSlash()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1366222207);
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\NodeInterface::class);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $structure = [
            'name' => 'foo/bar',
        ];
        $node->__construct($structure, $parent);
    }

    /**
     * @test
     */
    public function constructorSetsParent()
    {
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\NodeInterface::class);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $structure = [
            'name' => 'foo',
        ];
        $node->__construct($structure, $parent);
        $this->assertSame($parent, $node->_call('getParent'));
    }

    /**
     * @test
     */
    public function constructorSetsTargetPermission()
    {
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\NodeInterface::class);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $targetPermission = '0660';
        $structure = [
            'name' => 'foo',
            'targetPermission' => $targetPermission,
        ];
        $node->__construct($structure, $parent);
        $this->assertSame($targetPermission, $node->_call('getTargetPermission'));
    }

    /**
     * @test
     */
    public function constructorSetsName()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\RootNodeInterface::class);
        $name = $this->getUniqueId('test_');
        $node->__construct(['name' => $name], $parent);
        $this->assertSame($name, $node->getName());
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionIfBothTargetContentAndTargetContentFileAreSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1380364361);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\RootNodeInterface::class);
        $structure = [
            'name' => 'foo',
            'targetContent' => 'foo',
            'targetContentFile' => 'aPath',
        ];
        $node->__construct($structure, $parent);
    }

    /**
     * @test
     */
    public function constructorSetsTargetContent()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\RootNodeInterface::class);
        $targetContent = $this->getUniqueId('content_');
        $structure = [
            'name' => 'foo',
            'targetContent' => $targetContent,
        ];
        $node->__construct($structure, $parent);
        $this->assertSame($targetContent, $node->_get('targetContent'));
    }

    /**
     * @test
     */
    public function constructorSetsTargetContentToContentOfTargetContentFile()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\RootNodeInterface::class);
        $targetFile = $this->getVirtualTestFilePath('test_');
        $targetContent = $this->getUniqueId('content_');
        file_put_contents($targetFile, $targetContent);
        $structure = [
            'name' => 'foo',
            'targetContentFile' => $targetFile,
        ];
        $node->__construct($structure, $parent);
        $this->assertSame($targetContent, $node->_get('targetContent'));
    }

    /**
     * @test
     */
    public function constructorThrowsExceptionIfTargetContentFileDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1380364362);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\RootNodeInterface::class);
        $targetFile = $this->getVirtualTestFilePath('test_');
        $structure = [
            'name' => 'foo',
            'targetContentFile' => $targetFile,
        ];
        $node->__construct($structure, $parent);
    }

    /**
     * @test
     */
    public function targetContentIsNullIfNotGiven()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['dummy'], [], '', false);
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\RootNodeInterface::class);
        $structure = [
            'name' => 'foo',
        ];
        $node->__construct($structure, $parent);
        $this->assertNull($node->_get('targetContent'));
    }

    /**
     * @test
     */
    public function getStatusReturnsArray()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['getAbsolutePath', 'exists', 'isFile', 'isWritable', 'isPermissionCorrect', 'isContentCorrect'],
            [],
            '',
            false
        );
        $path = PATH_site . 'typo3temp/var/tests/' . $this->getUniqueId('dir_');
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $node->expects($this->any())->method('isWritable')->will($this->returnValue(true));
        $node->expects($this->any())->method('isContentCorrect')->will($this->returnValue(true));
        $this->assertInternalType('array', $node->getStatus());
    }

    /**
     * @test
     */
    public function getStatusReturnsArrayWithWarningStatusIFileNotExists()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['getAbsolutePath', 'getRelativePathBelowSiteRoot', 'exists', 'isFile', 'isWritable', 'isPermissionCorrect', 'isContentCorrect'],
            [],
            '',
            false
        );
        $path = $this->getVirtualTestDir('dir_');
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $node->expects($this->any())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $node->expects($this->any())->method('isWritable')->will($this->returnValue(true));
        $node->expects($this->any())->method('isContentCorrect')->will($this->returnValue(true));
        $statusArray = $node->getStatus();
        $this->assertSame(FlashMessage::WARNING, $statusArray[0]->getSeverity());
    }

    /**
     * @test
     */
    public function getStatusReturnsArrayWithErrorStatusIfNodeIsNotAFile()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['getAbsolutePath', 'getRelativePathBelowSiteRoot', 'exists', 'isFile', 'isWritable', 'isPermissionCorrect', 'isContentCorrect'],
            [],
            '',
            false
        );
        $path = $this->getVirtualTestFilePath('dir_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(false));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $node->expects($this->any())->method('isWritable')->will($this->returnValue(true));
        $node->expects($this->any())->method('isContentCorrect')->will($this->returnValue(true));
        $statusArray = $node->getStatus();
        $this->assertSame(FlashMessage::ERROR, $statusArray[0]->getSeverity());
    }

    /**
     * @test
     */
    public function getStatusReturnsArrayNoticeStatusIfFileExistsButIsNotWritable()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['getAbsolutePath', 'getRelativePathBelowSiteRoot', 'exists', 'isFile', 'isWritable', 'isPermissionCorrect', 'isContentCorrect'],
            [],
            '',
            false
        );
        $path = $this->getVirtualTestFilePath('dir_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $node->expects($this->any())->method('isWritable')->will($this->returnValue(false));
        $node->expects($this->any())->method('isContentCorrect')->will($this->returnValue(true));
        $statusArray = $node->getStatus();
        $this->assertSame(FlashMessage::NOTICE, $statusArray[0]->getSeverity());
    }

    /**
     * @test
     */
    public function getStatusReturnsArrayWithNoticeStatusIfFileExistsButPermissionAreNotCorrect()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['getAbsolutePath', 'getRelativePathBelowSiteRoot', 'exists', 'isFile', 'isWritable', 'isPermissionCorrect', 'isContentCorrect'],
            [],
            '',
            false
        );
        $path = $this->getVirtualTestFilePath('dir_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(false));
        $node->expects($this->any())->method('isWritable')->will($this->returnValue(true));
        $node->expects($this->any())->method('isContentCorrect')->will($this->returnValue(true));
        $statusArray = $node->getStatus();
        $this->assertSame(FlashMessage::NOTICE, $statusArray[0]->getSeverity());
    }

    /**
     * @test
     */
    public function getStatusReturnsArrayWithNoticeStatusIfFileExistsButContentIsNotCorrect()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['getAbsolutePath', 'getRelativePathBelowSiteRoot', 'exists', 'isFile', 'isWritable', 'isPermissionCorrect', 'isContentCorrect'],
            [],
            '',
            false
        );
        $path = $this->getVirtualTestFilePath('dir_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $node->expects($this->any())->method('isWritable')->will($this->returnValue(true));
        $node->expects($this->any())->method('isContentCorrect')->will($this->returnValue(false));
        $statusArray = $node->getStatus();
        $this->assertSame(FlashMessage::NOTICE, $statusArray[0]->getSeverity());
    }

    /**
     * @test
     */
    public function getStatusReturnsArrayWithOkStatusIfFileExistsAndPermissionAreCorrect()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['getAbsolutePath', 'getRelativePathBelowSiteRoot', 'exists', 'isFile', 'isWritable', 'isPermissionCorrect', 'isContentCorrect'],
            [],
            '',
            false
        );
        $path = $this->getVirtualTestFilePath('dir_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $node->expects($this->any())->method('isWritable')->will($this->returnValue(true));
        $node->expects($this->any())->method('isContentCorrect')->will($this->returnValue(true));
        $statusArray = $node->getStatus();
        $this->assertSame(FlashMessage::OK, $statusArray[0]->getSeverity());
    }

    /**
     * @test
     */
    public function fixCallsFixSelfAndReturnsItsResult()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['fixSelf'],
            [],
            '',
            false
        );
        $uniqueReturn = [$this->getUniqueId('foo_')];
        $node->expects($this->once())->method('fixSelf')->will($this->returnValue($uniqueReturn));
        $this->assertSame($uniqueReturn, $node->fix());
    }

    /**
     * @test
     */
    public function fixSelfCallsCreateFileIfFileDoesNotExistAndReturnsResult()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['exists', 'createFile', 'setContent', 'getAbsolutePath', 'isFile', 'isPermissionCorrect'],
            [],
            '',
            false
        );
        $node->expects($this->any())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $message = new FlashMessage('foo');
        $node->expects($this->once())->method('createFile')->will($this->returnValue($message));
        $actualReturn = $node->_call('fixSelf');
        $actualReturn = $actualReturn[0];
        $this->assertSame($message, $actualReturn);
    }

    /**
     * @test
     */
    public function fixSelfCallsSetsContentIfFileCreationWasSuccessfulAndTargetContentIsNotNullAndReturnsResult()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['exists', 'createFile', 'setContent', 'getAbsolutePath', 'isFile', 'isPermissionCorrect'],
            [],
            '',
            false
        );
        $node->expects($this->any())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $message1 = new FlashMessage('foo');
        $node->expects($this->any())->method('createFile')->will($this->returnValue($message1));
        $node->_set('targetContent', 'foo');
        $message2 = new FlashMessage('foo');
        $node->expects($this->once())->method('setContent')->will($this->returnValue($message2));
        $actualReturn = $node->_call('fixSelf');
        $actualReturn = $actualReturn[1];
        $this->assertSame($message2, $actualReturn);
    }

    /**
     * @test
     */
    public function fixSelfDoesNotCallSetContentIfFileCreationFailed()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['exists', 'createFile', 'setContent', 'getAbsolutePath', 'isFile', 'isPermissionCorrect'],
            [],
            '',
            false
        );
        $node->expects($this->any())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $message = new FlashMessage('foo', '', FlashMessage::ERROR);
        $node->expects($this->any())->method('createFile')->will($this->returnValue($message));
        $node->_set('targetContent', 'foo');
        $node->expects($this->never())->method('setContent');
        $node->_call('fixSelf');
    }

    /**
     * @test
     */
    public function fixSelfDoesNotCallSetContentIfFileTargetContentIsNull()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['exists', 'createFile', 'setContent', 'getAbsolutePath', 'isFile', 'isPermissionCorrect'],
            [],
            '',
            false
        );
        $node->expects($this->any())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $message = new FlashMessage('foo');
        $node->expects($this->any())->method('createFile')->will($this->returnValue($message));
        $node->_set('targetContent', null);
        $node->expects($this->never())->method('setContent');
        $node->_call('fixSelf');
    }

    /**
     * @test
     */
    public function fixSelfReturnsErrorStatusIfNodeExistsButIsNotAFileAndReturnsResult()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['exists', 'createFile', 'getAbsolutePath', 'isFile', 'isPermissionCorrect', 'fixPermission'],
            [],
            '',
            false
        );
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(false));
        $message = new FlashMessage('foo');
        $node->expects($this->once())->method('fixPermission')->will($this->returnValue($message));
        $this->assertSame([$message], $node->_call('fixSelf'));
    }

    /**
     * @test
     */
    public function fixSelfCallsFixPermissionIfFileExistsButPermissionAreWrong()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['exists', 'createFile', 'getAbsolutePath', 'isFile', 'isPermissionCorrect', 'getRelativePathBelowSiteRoot'],
            [],
            '',
            false
        );
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->once())->method('isFile')->will($this->returnValue(false));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $resultArray = $node->_call('fixSelf');
    }

    /**
     * @test
     */
    public function fixSelfReturnsArrayOfStatusMessages()
    {
        $node = $this->getAccessibleMock(
            FileNode::class,
            ['exists', 'isFile', 'isPermissionCorrect'],
            [],
            '',
            false
        );
        $node->expects($this->any())->method('exists')->will($this->returnValue(true));
        $node->expects($this->any())->method('isFile')->will($this->returnValue(true));
        $node->expects($this->any())->method('isPermissionCorrect')->will($this->returnValue(true));
        $this->assertInternalType('array', $node->_call('fixSelf'));
    }

    /**
     * @test
     */
    public function createFileThrowsExceptionIfNodeExists()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(1366398198);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['exists', 'getAbsolutePath'], [], '', false);
        $node->expects($this->once())->method('getAbsolutePath')->will($this->returnValue(''));
        $node->expects($this->once())->method('exists')->will($this->returnValue(true));
        $node->_call('createFile');
    }

    /**
     * @test
     */
    public function createFileReturnsOkStatusIfFileWasCreated()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['exists', 'getAbsolutePath', 'getRelativePathBelowSiteRoot'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        $node->expects($this->once())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $this->assertSame(FlashMessage::OK, $node->_call('createFile')->getSeverity());
    }

    /**
     * @test
     */
    public function createFileCreatesFile()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['exists', 'getAbsolutePath', 'getRelativePathBelowSiteRoot'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        $node->expects($this->once())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($path));
        $node->_call('createFile');
        $this->assertTrue(is_file($path));
    }

    /**
     * @test
     */
    public function createFileReturnsErrorStatusIfFileWasNotCreated()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['exists', 'getAbsolutePath', 'getRelativePathBelowSiteRoot'], [], '', false);
        $path = $this->getVirtualTestDir();
        chmod($path, 02550);
        $subPath = $path . '/' . $this->getUniqueId('file_');
        $node->expects($this->once())->method('exists')->will($this->returnValue(false));
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($subPath));
        $node->expects($this->any())->method('getRelativePathBelowSiteRoot')->will($this->returnValue($subPath));
        $this->assertSame(FlashMessage::ERROR, $node->_call('createFile')->getSeverity());
    }

    /**
     * @test
     */
    public function isContentCorrectThrowsExceptionIfTargetIsNotAFile()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(1367056363);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath'], [], '', false);
        $path = $this->getVirtualTestDir('dir_');
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->_call('isContentCorrect');
    }

    /**
     * @test
     */
    public function isContentCorrectReturnsTrueIfTargetContentPropertyIsNull()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->_set('targetContent', null);
        $this->assertTrue($node->_call('isContentCorrect'));
    }

    /**
     * @test
     */
    public function isContentCorrectReturnsTrueIfTargetContentEqualsCurrentContent()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        $content = $this->getUniqueId('content_');
        file_put_contents($path, $content);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->_set('targetContent', $content);
        $this->assertTrue($node->_call('isContentCorrect'));
    }

    /**
     * @test
     */
    public function isContentCorrectReturnsFalseIfTargetContentNotEqualsCurrentContent()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        $content = $this->getUniqueId('content1_');
        $targetContent = $this->getUniqueId('content2_');
        file_put_contents($path, $content);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->_set('targetContent', $targetContent);
        $this->assertFalse($node->_call('isContentCorrect'));
    }

    /**
     * @test
     */
    public function isPermissionCorrectReturnsTrueIfTargetPermissionAndCurrentPermissionAreIdentical()
    {
        $parent = $this->createMock(\TYPO3\CMS\Install\FolderStructure\NodeInterface::class);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getCurrentPermission', 'isWindowsOs'], [], '', false);
        $node->expects($this->any())->method('isWindowsOs')->will($this->returnValue(false));
        $node->expects($this->any())->method('getCurrentPermission')->will($this->returnValue('0664'));
        $targetPermission = '0664';
        $structure = [
            'name' => 'foo',
            'targetPermission' => $targetPermission,
        ];
        $node->__construct($structure, $parent);
        $this->assertTrue($node->_call('isPermissionCorrect'));
    }

    /**
     * @test
     */
    public function setContentThrowsExceptionIfTargetIsNotAFile()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(1367060201);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath'], [], '', false);
        $path = $this->getVirtualTestDir('dir_');
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->_set('targetContent', 'foo');
        $node->_call('setContent');
    }

    /**
     * @test
     */
    public function setContentThrowsExceptionIfTargetContentIsNull()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(1367060202);
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $node->_set('targetContent', null);
        $node->_call('setContent');
    }

    /**
     * @test
     */
    public function setContentSetsContentToFile()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath', 'getRelativePathBelowSiteRoot'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $targetContent = $this->getUniqueId('content_');
        $node->_set('targetContent', $targetContent);
        $node->_call('setContent');
        $resultContent = file_get_contents($path);
        $this->assertSame($targetContent, $resultContent);
    }

    /**
     * @test
     */
    public function setContentReturnsOkStatusIfContentWasSuccessfullySet()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath', 'getRelativePathBelowSiteRoot'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $targetContent = $this->getUniqueId('content_');
        $node->_set('targetContent', $targetContent);
        $this->assertSame(FlashMessage::OK, $node->_call('setContent')->getSeverity());
    }

    /**
     * @test
     */
    public function setContentReturnsErrorStatusIfContentCanNotBeSetSet()
    {
        if (function_exists('posix_getegid') && posix_getegid() === 0) {
            $this->markTestSkipped('Test skipped if run on linux as root');
        }
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath', 'getRelativePathBelowSiteRoot'], [], '', false);
        $dir = $this->getVirtualTestDir('dir_');
        $file = $dir . '/' . $this->getUniqueId('file_');
        touch($file);
        chmod($file, 0440);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($file));
        $targetContent = $this->getUniqueId('content_');
        $node->_set('targetContent', $targetContent);
        $this->assertSame(FlashMessage::ERROR, $node->_call('setContent')->getSeverity());
    }

    /**
     * @test
     */
    public function isFileReturnsTrueIfNameIsFile()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath', 'getRelativePathBelowSiteRoot'], [], '', false);
        $path = $this->getVirtualTestFilePath('file_');
        touch($path);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path));
        $this->assertTrue($node->_call('isFile'));
    }

    /**
     * @test
     * @see https://github.com/mikey179/vfsStream/wiki/Known-Issues - symlink doesn't work with vfsStream
     */
    public function isFileReturnsFalseIfNameIsALinkFile()
    {
        /** @var $node FileNode|AccessibleObjectInterface|\PHPUnit_Framework_MockObject_MockObject */
        $node = $this->getAccessibleMock(FileNode::class, ['getAbsolutePath'], [], '', false);
        $path = PATH_site . 'typo3temp/var/tests/' . $this->getUniqueId('root_');
        \TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep($path);
        $this->testFilesToDelete[] = $path;
        $link = $this->getUniqueId('link_');
        $file = $this->getUniqueId('file_');
        touch($path . '/' . $file);
        symlink($path . '/' . $file, $path . '/' . $link);
        $node->expects($this->any())->method('getAbsolutePath')->will($this->returnValue($path . '/' . $link));
        $this->assertFalse($node->_call('isFile'));
    }
}
