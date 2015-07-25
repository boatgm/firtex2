#include "firtex/extension/util/ServerApplication.h"
#include "WatchdogAppRunner.h"
#include "firtex/utility/Path.h"
#include "firtex/config/XMLConfigurator.h"

using namespace std;
FX_NS_USE(app);
FX_NS_USE(util);
FX_NS_USE(common);
FX_NS_USE(config);
FX_NS_USE(utility);

#define LOGGER_CONF_FILE_NAME "logger_conf.xml"

int main(int argc,char* argv[])
{
    Path logFilePath(Path::current());
    logFilePath.makeDirectory();
    logFilePath.setFileName(LOGGER_CONF_FILE_NAME);

    string sLogConf = logFilePath.toString();
    File confFile(sLogConf);
    if (confFile.exists())
    {
        cout << "[INFO] Load logger configure file: [" << sLogConf << "]" << endl;
        LoggerBuilder::instance()->configure(sLogConf);
    }

    ServerApplication app("fdaemon", "FirteX daemon utility.");
    app.addAppRunner(new WatchdogAppRunner);
    if (app.parse(argc, (const char**)argv))
    {
        Application::Status stat =  app.run();
        return stat;
    }
    return 0;
}
