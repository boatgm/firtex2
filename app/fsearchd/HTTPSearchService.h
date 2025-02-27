#ifndef __FX_HTTPSEARCHSERVICE_H
#define __FX_HTTPSEARCHSERVICE_H

#include "SearchResource.h"
#include "firtex/queryparser/QueryParser.h"
#include "firtex/search/QueryHits.h"
#include "firtex/search/Statement.h"
#include "firtex/search/QueryResult.h"
#include "firtex/extension/network/EvHttpService.h"
#include "firtex/extension/network/EvHttpRequestContext.h"
#include "firtex/utility/StringUtils.h"

FX_NS_DEF(app);

class HTTPSearchService : public FX_NS(network)::EvHttpService
{
public:
    const static std::string URLENCODE_PRIFIX;

public:
    HTTPSearchService(const SearchResource& searchRes);

public:
    /**
     * Return requsets this service can handle
     * @return the requests string
     */
    std::string requestCanHandle() const;

    /**
     * Handle a http request. This method must be thread-safe
     * @param pCtx request context
     */
    virtual void handleRequest(FX_NS(network)::EvHttpRequestContext* pCtx);

private:
    void handleQuery(const FX_NS(search)::Statement& state,
                     FX_NS(network)::EvHttpRequestContext* pCtx) const;

    void sendResponse(const std::string& sMsg,
                      FX_NS(network)::EvHttpRequestContext* pCtx) const;

    void sendErrorMessage(const std::string& sErrorMsg,
                          FX_NS(network)::EvHttpRequestContext* pCtx) const;

private:
    inline void doSearch(const std::string& sUri,
                         FX_NS(network)::EvHttpRequestContext* pCtx) const;


private:
    const SearchResource& m_searchRes;
    std::string m_sEncoding;

private:
    DECLARE_STREAM_LOGGER();
};

//////////////////////////////////////////
//
inline void HTTPSearchService::doSearch(const std::string& sUri,
                                    FX_NS(network)::EvHttpRequestContext* pCtx) const
{
    FX_NS(search)::Statement state;
    bool bValid = state.fromString(sUri);
    if (!bValid)
    {
        sendErrorMessage(state.getErrorMessage(), pCtx);
    }    
    else
    {
        handleQuery(state, pCtx);
    }
}

inline void HTTPSearchService::sendResponse(const std::string& sMsg,
        FX_NS(network)::EvHttpRequestContext* pCtx) const
{
    pCtx->getOutBuffer()->addData(sMsg.c_str(), sMsg.length());
}

FX_NS_END

#endif //__FX_SEARCH_SERVICE_H
