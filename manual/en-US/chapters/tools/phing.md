# PHING

We recommend using Gulp for redCORE development instead of PHING but if you cannot use Gulp for some reason, we still support PHING build and deploy scripts which you can use to quickly create a release or to copy all new/updated files into the testing site.

## What is PHING?
PHing Is Not GNU make; it's a PHP project build system or build tool based on ​Apache Ant. You can do anything with it that you could do with a traditional build system like GNU make, and its use of simple XML build files and extensible PHP "task" classes make it an easy-to-use and highly flexible build framework.

redCORE provides several PHING scripts to be used by the developers to automate tasks like packaging the extension.

## Packaging redCORE inside your extension
To add redCORE inside your extension we use a sub PHING script. This subscript ensures that the redCORE is added to your 
extension uses the less space as possible by removing uncompressed files: JS, CSS,...

See the target "copyredcore" of the following extension PHING packager script:

```
	...	   
	<!-- The following target will call the copyredcore script that copies only the compressed files  -->
    <target name="copyredcore">
        <phing phingfile="${project.basedir}/redCORE/build/redcore_copy_mandatory.xml"
               target="copyframework"
               haltonfailure="true">
            <property name="redcoretargetdir" value="${targetdir}/redCORE" />
           <property name="redcorepath" value="${project.basedir}/redCORE" />
        </phing>
    </target>
 
    <!-- ============================================  -->
    <!-- (DEFAULT)  Target: dist                       -->
    <!-- ============================================  -->
 
    <target name="dist" depends="build, copyredcore">
        <echo msg="Creating ZIP archive..."/>
        <zip destfile="${temp.dir}/../${extension}-${version}.zip">
            <fileset dir="${targetdir}">
                <include name="**"/>
                <exclude name=".*"/>
            </fileset>
        </zip>
 
        <echo msg="Files copied and compressed in build directory OK!"/>
        <delete dir="${temp.dir}" />
    </target>
```

Where:

* ${targetdir} is the folder where you want to place the compressed .zip package. See [https://github.com/redCOMPONENT-COM/redCORE/blob/develop/build/build.properties.dist#L8](https://github.com/redCOMPONENT-COM/redCORE/blob/develop/build/build.properties.dist#L8).
* ${version} is the variable that contains the version number of your extension. In redCORE PHING script we use the following code to get the version number straight from the component xml manifest: [https://github.com/redCOMPONENT-COM/redCORE/blob/develop/build/extension_packager.xml#L24-L27](https://github.com/redCOMPONENT-COM/redCORE/blob/develop/build/extension_packager.xml#L24-L27).
    
If you want to use the previous script for package Your Properties file look at the PHING extension packager provided at redCORE: [https://github.com/redCOMPONENT-COM/redCORE/blob/develop/build/extension_packager.xml](https://github.com/redCOMPONENT-COM/redCORE/blob/develop/build/extension_packager.xml)
